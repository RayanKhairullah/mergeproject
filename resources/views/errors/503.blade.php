@extends('errors.illustrated')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('Under Maintenance'))
@section('description', __('We are currently performing some scheduled maintenance. We will be back online shortly!'))
