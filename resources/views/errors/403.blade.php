@extends('errors.illustrated')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __('Access Denied'))
@section('description', __($exception->getMessage() ?: 'You do not have the necessary permissions to access this resource.'))
