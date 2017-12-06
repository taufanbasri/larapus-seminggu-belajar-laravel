<?php

namespace App\Http\Controllers;

use App\Author;
use Illuminate\Http\Request;
use DataTables;
use Yajra\DataTables\Html\Builder;
use Session;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
      if ($request->ajax()) {
        $authors = Author::select(['id', 'name']);
        return Datatables::of($authors)
          ->addColumn('action', function($author){
            return view('datatable._action', [
              'model' => $author,
              'form_url' => route('authors.destroy', $author->id),
              'edit_url' => route('authors.edit', $author->id),
            ]);
          })
        ->toJson();
      }

      $html = $htmlBuilder
        ->columns([
          ['data' => 'name', 'name' => 'name', 'title'=>'Nama'],
          ['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable' => false]
        ]);

      return view('authors.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('authors.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, ['name' => 'required|unique:authors']);

      $author = Author::create($request->all());

      Session::flash("flash_notification", [
        "level" => "success",
        "message" => "Berhasil menyimpan $author->name"
      ]);

      return redirect()->route('authors.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function show(Author $author)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function edit(Author $author)
    {
        return view('authors.edit')->with(compact('author'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Author $author)
    {
        $this->validate($request, ['name' => 'required|unique:authors,name,'. $author->id]);

        $author->update($request->only('name'));

        Session::flash("flash_notification", [
          "level" => "success",
          "message" => "Berhasil menyimpan $author->name"
        ]);

        return redirect()->route('authors.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function destroy(Author $author)
    {
      $author->delete();

      Session::flash("flash_notification", [
        "level"=>"success",
        "message"=>"Penulis berhasil dihapus"
      ]);

      return redirect()->route('authors.index');
    }
}
