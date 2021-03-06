<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Builder;
use DataTables;
use Session;
use File;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\BookRequest;
use App\BorrowLog;
use App\Exceptions\BookException;

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
    public function store(BookRequest $request)
    {
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
    public function update(BookRequest $request, Book $book)
    {
      if (!$book->update($request->all())) {
        return redirect()->back();
      }

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
      $cover = $book->cover;

      if (!$book->delete()) {
        return redirect()->back();
      }

      if ($cover) {
        $this->deleteCover($book);
      }

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

    public function borrow(Book $book)
    {
      try {
        auth()->user()->borrow($book);

        Session::flash("flash_notification", [
          "level"=>"success",
          "message"=>"Berhasil meminjam $book->title"
        ]);
      } catch (BookException $e) {
        Session::flash("flash_notification", [
          "level" => "danger",
          "message" => $e->getMessage()
        ]);
      } catch (ModelNotFoundException $e) {
        Session::flash("flash_notification", [
          "level"=>"danger",
          "message"=>"Buku tidak ditemukan."
        ]);
      }

      return redirect('/');;
    }

    public function returnBack(Book $book)
    {
      $borrowLog = BorrowLog::where([
        ['user_id', auth()->user()->id],
        ['book_id', $book->id],
        ['is_returned', 0]
      ])->first();

      if ($borrowLog) {
        $borrowLog->is_returned = true;
        $borrowLog->save();

        Session::flash("flash_notification", [
          "level" => "success",
          "message" => "Berhasil mengembalikan " . $borrowLog->book->title
        ]);
      }

      return redirect('/home');
    }
}
