@extends('errors.illustrated')

@section('title', __('Payload Too Large'))
@section('code', '413')
@section('message', __('File Too Big'))
@section('description', __('The file you are trying to upload is too large for the system to process. Please try a smaller file.'))
