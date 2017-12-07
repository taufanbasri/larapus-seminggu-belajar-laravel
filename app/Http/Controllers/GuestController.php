<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Html\Builder;
use DataTables;
use App\Book;
use Laratrust\LaratrustFacade as Laratrust;

class GuestController extends Controller
{
  public function index(Request $request, Builder $htmlBuilder)
  {
    if ($request->ajax()) {
      $books = Book::with('author');

      return Datatables::of($books)
        ->addColumn('action', function($book){
          if (Laratrust::hasRole('admin')) return '';
          return '<a class="btn btn-xs btn-primary" href="#">Pinjam</a>';
        })->tojSon();
    }

    $html = $htmlBuilder->columns([
      ['data' => 'title', 'name'=>'title', 'title'=>'Judul'],
      ['data' => 'author.name', 'name'=>'author.name', 'title'=>'Penulis'],
      ['data' => 'action', 'name'=>'action', 'title'=>'', 'orderable'=>false, 'searchable'=>false]
    ]);

    return view('guest.index')->with(compact('html'));
  }
}
