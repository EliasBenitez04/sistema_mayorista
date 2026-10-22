@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-images text-primary mr-2"></i>Fotografías cargadas</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right" href="{{ route('carga_fotos.create') }}">
                        <i class="fas fa-plus mr-1"></i> Nueva foto
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        

        <form id="form-busqueda-fotos" method="GET" action="{{ route('carga_fotos.index') }}">
            <div class="input-group mb-3">
                <input type="text" class="form-control buscar-fotos" name="search"
                    value="{{ request()->get('search', '') }}" placeholder="Buscar por OT, descripción o línea..."
                    data-url="{{ route('carga_fotos.index') }}">
                <button class="btn btn-outline-secondary" type="submit">Buscar</button>
            </div>
        </form>

        <div id="tabla-fotos-container">
            @include('carga_fotos.table', ['fotos' => $fotos])
    </div>

    </div>
@endsection


@push('page_scripts')
<script>
document.addEventListener('DOMContentLoaded',function(){
 const form=document.getElementById('form-busqueda-fotos');
 const input=form.querySelector('input[name="search"]');
 const container=document.getElementById('tabla-fotos-container');
 let timer=null;
 let controller=null;
 async function load(url){
  if(controller)controller.abort();
  controller=new AbortController();
  try{
   const response=await fetch(url,{headers:{'X-Requested-With':'XMLHttpRequest'},signal:controller.signal});
   if(!response.ok)throw new Error('Error de búsqueda');
   container.innerHTML=await response.text();
  }catch(error){
   if(error.name!=='AbortError')console.error(error);
  }
 }
 input.addEventListener('input',function(){
  clearTimeout(timer);
  timer=setTimeout(function(){
   const url=new URL(form.action);
   url.searchParams.set('search',input.value.trim());
   load(url.toString());
  },350);
 });
 form.addEventListener('submit',function(event){
  event.preventDefault();
  clearTimeout(timer);
  const url=new URL(form.action);
  url.searchParams.set('search',input.value.trim());
  load(url.toString());
 });
 container.addEventListener('click',function(event){
  const link=event.target.closest('.pagination a');
  if(!link)return;
  event.preventDefault();
  load(link.href);
 });
});
</script>
@endpush
