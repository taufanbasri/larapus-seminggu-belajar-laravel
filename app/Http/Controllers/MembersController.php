<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Html\Builder;
use DataTables;
use App\Role;
use App\User;
use App\Http\Requests\MemberRequest;
use Session;
use Mail;

class MembersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
      if ($request->ajax()) {
        $members = Role::where('name', 'member')->first()->users;

        return Datatables::of($members)
          ->addColumn('action', function($member){
            return view('datatable._action', [
            'model' => $member,
            'form_url' => route('members.destroy', $member->id),
            'edit_url' => route('members.edit', $member->id),
            'confirm_message' => 'Yakin mau menghapus ' . $member->name . '?'
            ]);
          })->toJson();
      }

      $html = $htmlBuilder->columns([
        ['data' => 'name', 'name'=>'name', 'title'=>'Nama'],
        ['data' => 'email', 'name'=>'email', 'title'=>'Email'],
        ['data' => 'action', 'name'=>'action', 'title'=>'', 'orderable' => false, 'searchable' => false]
      ]);

      return view('members.index', compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('members.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MemberRequest $request)
    {
      $password = str_random(6);
      $data = $request->all();
      $data['password'] = bcrypt($password);
      // bypass verifikasi
      $data['is_verified'] = 1;

      $member = User::create($data);
      // set role
      $memberRole = Role::where('name', 'member')->first();
      $member->attachRole($memberRole);

      // kirim email
      Mail::send('auth.emails.invite', compact('member', 'password'), function ($m) use ($member) {
        $m->to($member->email, $member->name)->subject('Anda telah didaftarkan di Larapus!');
      });

      Session::flash("flash_notification", [
      "level" => "success",
      "message" => "Berhasil menyimpan member dengan email " .
                  "<strong>" . $data['email'] . "</strong>" .
                  " dan password <strong>" . $password . "</strong>."
      ]);

      return redirect()->route('members.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $member = User::find($id);

        return view('members.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MemberRequest $request, $id)
    {
      $member = User::find($id);
      $member->update($request->only('name','email'));

      Session::flash("flash_notification", [
        "level"=>"success",
        "message"=>"Berhasil menyimpan $member->name"
      ]);

      return redirect()->route('members.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
