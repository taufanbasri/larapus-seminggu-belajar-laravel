<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Builder;
use DataTables;
use Session;
use File;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
      if ($request->ajax()) {
        $books = Book::with('author');

        return Datatables::of($books)
          ->addColumn('action', function($book){
            return view('datatable._action', [
              'model' => $book,
              'form_url' => route('books.destroy', $book->id),
              'edit_url' => route('books.edit', $book->id),
              'confirm_message' => 'Yakin akan menghapus ' . $book->title . '?'
            ]);
          })->toJson();
      }

      $html = $htmlBuilder
        ->columns([
          ['data' => 'title', 'name' => 'title', 'title' => 'Judul'],
          ['data' => 'amount', 'name' => 'amount', 'title' => 'Jumlah'],
          ['data' => 'author.name', 'name' => 'author.name', 'title' => 'Penulis'],
          ['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable' => false]
        ]);

      return view('books.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('books.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'title' => 'required|unique:books,title',
        'author_id' => 'required|exists:authors,id',
        'amount' => 'required|numeric',
        'cover' => 'image|max:2048'
      ]);

      $book = Book::create($request->except('cover'));

      if ($request->hasFile('cover')) {
        $uploaded_cover = $request->file('cover');
        $extension = $uploaded_cover->getClientOriginalExtension();
        $filename = md5(time()) . '.' . $extension;
        $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'img';
        $uploaded_cover->move($destinationPath, $filename);
        $book->cover = $filename;
        $book->save();
      }

      Session::flash("flash_notification", [
      "level"=>"success",
      "message"=>"Berhasil menyimpan $book->title"
      ]);

      return redirect()->route('books.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        return view('books.edit')->with(compact('book'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
      $this->validate($request, [
        'title' => 'required|unique:books,title,' . $book->id,
        'author_id' => 'required|exists:authors,id',
        'amount' => 'required|numeric',
        'cover' => 'image|max:2048'
      ]);

      $book->update($request->all());

      if ($request->hasFile('cover')) {
        $filename = null;
        $uploaded_cover = $request->file('cover');
        $extension = $uploaded_cover->getClientOriginalExtension();
        $filename = md5(time()) . '.' . $extension;
        $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'img';
        $uploaded_cover->move($destinationPath, $filename);

        if ($book->cover) {
          $this->deleteCover($book);
        }

        $book->cover = $filename;
        $book->save();
      }

      Session::flash("flash_notification", [
        "level"=>"success",
        "message"=>"Berhasil menyimpan $book->title"
      ]);

      return redirect()->route('books.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
      if ($book->cover) {
        $this->deleteCover($book);
      }

      $book->delete();

      Session::flash("flash_notification", [
        "level"=>"success",
        "message"=>"Buku berhasil dihapus"
      ]);

      return redirect()->route('books.index');
    }

    public function deleteCover($book)
    {
      $old_cover = $book->cover;
      $filepath = public_path() . DIRECTORY_SEPARATOR . 'img'
      . DIRECTORY_SEPARATOR . $book->cover;

      try {
        File::delete($filepath);
      } catch (FileNotFoundException $e) {
        // File sudah dihapus/tidak ada
      }
    }
}
