@extends('errors.illustrated')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('Slow Down!'))
@section('description', __('You have made too many requests in a short period of time. Please wait a moment before trying again.'))
