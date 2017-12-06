{!! Form::model($model, ['url' => $form_url, 'method' => 'delete', 'class' => 'form-inline']) !!}
  <a class="btn btn-xs btn-warning" href="{{ $edit_url }}">Ubah</a> |
  {!! Form::submit('Hapus', ['class'=>'btn btn-xs btn-danger']) !!}
{!! Form::close()!!}
