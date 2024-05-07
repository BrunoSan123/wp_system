<!-- resources/views/changelog.blade.php -->
@extends('layouts.app')

@php
    use App\Models\Editor;
    use App\Models\Wp_credential;
    use Illuminate\Support\Facades\Http;



    $valorCodificado = request()->cookie('editor');
    $user = explode('+', base64_decode($valorCodificado));
    $post_configs = Editor::where('name', $user[0])->first();
@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Changelog</div>

                <div class="card-body">
                    <table class="table changelog-table">
                        <thead>
                            <tr>
                                <th>Commit</th>
                                <th>Autor</th>
                                <th>Email</th>
                                <th>Mensagem</th>
                                <th>Comentario</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($changelog as $line)
                                @php
                                    $fields = explode(',', $line);
                                @endphp
                                <tr>
                                    @foreach ($fields as $field)
                                        <td>{{ $field }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.changelog-table th,
.changelog-table td {
    padding: 8px;
    text-align: left;
}

.changelog-table th {
    background-color: #f8f9fa;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #ccc;
    padding: 10px;
    font-weight: bold;
}

</style>
@endsection
