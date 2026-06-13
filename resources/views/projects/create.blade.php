@extends('layouts.app')
@section('title', 'Nouveau projet')
@section('page-title', 'NOUVEAU PROJET')
@section('content')
@include('projects._form', ['project' => null, 'selectedTags' => []])
@endsection
