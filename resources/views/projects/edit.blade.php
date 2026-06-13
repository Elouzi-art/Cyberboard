@extends('layouts.app')
@section('title', 'Modifier — ' . $project->title)
@section('page-title', 'MODIFIER PROJET')
@section('content')
@include('projects._form', ['project' => $project, 'selectedTags' => $selectedTags])
@endsection
