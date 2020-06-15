@extends('layouts.app')

@section('content')


<div class="container">
 <div v-if="activetab=='main'">
    <read :key="'1'" :modelname="'Todo'"></read>
    {{-- <edit :key="'2'" :modelname="'Todo'"></edit> --}}
 </div>

    
</div>



@endsection
