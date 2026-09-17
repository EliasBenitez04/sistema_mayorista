<div class="col-12">
    <div class="pedido-form-section">
        <div class="pedido-form-section-header">
            <div class="pedido-form-section-icon"><i class="fas fa-file-invoice"></i></div>
            <div>
                <h3>Información del pedido</h3>
                <p>Datos generales, cliente, condición comercial y observaciones.</p>
            </div>
        </div>

        <div class="pedido-form-section-body">
            <div class="row">
                <div class="form-group col-xl-3 col-md-6">
                    {!! Form::label('nro_pedido', 'N° Pedido', ['class' => 'pedido-field-label']) !!}
                    <div class="input-group pedido-input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-hashtag"></i></span></div>
                        {!! Form::text('nro_pedido', $pedido->nro_pedido ?? $nroPedidoPreview, ['class' => 'form-control', 'readonly' => true]) !!}
                    </div>
                </div>

                <div class="form-group col-xl-3 col-md-6">
                    {!! Form::label('ped_fecha', 'Fecha', ['class' => 'pedido-field-label']) !!}
                    <div class="input-group pedido-input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                        {!! Form::date('ped_fecha', isset($pedido) && !empty($pedido->ped_fecha) ? \Carbon\Carbon::parse($pedido->ped_fecha)->format('Y-m-d') : \Carbon\Carbon::now()->format('Y-m-d'), ['class' => 'form-control', 'id' => 'ped_fecha']) !!}
                    </div>
                </div>

                <div class="form-group col-xl-3 col-md-6">
                    {!! Form::label('user_id', 'Usuario', ['class' => 'pedido-field-label']) !!}
                    <div class="input-group pedido-input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                        {!! Form::text('user_id', Auth::user()->name, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                    </div>
                </div>

                @php $codSucursalPedido = $pedido->cod_suc ?? Auth::user()->cod_suc; @endphp
                <div class="form-group col-xl-3 col-md-6">
                    {!! Form::label('cod_suc', 'Sucursal', ['class' => 'pedido-field-label']) !!}
                    <div class="input-group pedido-input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-store"></i></span></div>
                        {!! Form::select('cod_suc_visual', $sucursal, $codSucursalPedido, ['class' => 'form-control', 'disabled' => true]) !!}
                    </div>
                    {!! Form::hidden('cod_suc', $codSucursalPedido, ['id' => 'cod_suc']) !!}
                </div>

                <div class="form-group col-xl-6 col-md-7">
                    {!! Form::label('id_cliente', 'Cliente', ['class' => 'pedido-field-label']) !!}
                    <div class="pedido-select-shell">
                        <span class="pedido-select-icon"><i class="fas fa-user-tie"></i></span>
                        <div class="pedido-select-control">
                            {!! Form::select('id_cliente', $clientes, $pedido->id_cliente ?? null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione un cliente', 'required' => true, 'style' => 'width:100%;']) !!}
                        </div>
                    </div>
                    <small class="pedido-field-help">Busque por CI/RUC o nombre del cliente.</small>
                </div>

                <div class="form-group col-xl-3 col-md-5">
                    {!! Form::label('condicion', 'Condición del pedido', ['class' => 'pedido-field-label']) !!}
                    <div class="input-group pedido-input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-credit-card"></i></span></div>
                        {!! Form::select('condicion', $condicion, $pedido->condicion ?? null, ['class' => 'form-control', 'id' => 'condicion']) !!}
                    </div>
                </div>

                <div class="form-group col-xl-3 col-md-6">
                    {!! Form::label('aplica_descuento', 'Aplicar descuento', ['class' => 'pedido-field-label d-block']) !!}
                    <div class="pedido-radio-group">
                        <label class="pedido-radio-option" for="descuento_si">
                            {!! Form::radio('aplica_descuento', 'SI', isset($pedido) && (float) ($pedido->descuento ?? 0) > 0, ['id' => 'descuento_si', 'class' => 'pedido-radio-input']) !!}
                            <span class="pedido-radio-box"><i class="fas fa-check"></i></span><span>Sí</span>
                        </label>
                        <label class="pedido-radio-option" for="descuento_no">
                            {!! Form::radio('aplica_descuento', 'NO', !isset($pedido) || (float) ($pedido->descuento ?? 0) <= 0, ['id' => 'descuento_no', 'class' => 'pedido-radio-input']) !!}
                            <span class="pedido-radio-box"><i class="fas fa-times"></i></span><span>No</span>
                        </label>
                    </div>
                </div>

                <div class="form-group col-xl-3 col-md-6" id="div-descuento" style="display:none;">
                    {!! Form::label('descuento', 'Descuento (%)', ['class' => 'pedido-field-label']) !!}
                    <div class="input-group pedido-input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-percent"></i></span></div>
                        {!! Form::number('descuento', isset($pedido) ? $pedido->descuento : null, ['class' => 'form-control', 'min' => 0, 'max' => 100, 'step' => '0.01', 'id' => 'descuento_input']) !!}
                    </div>
                    <small class="text-danger d-none" id="error-descuento">El descuento máximo permitido es 100%</small>
                </div>

                <div class="form-group col-xl-3 col-md-6" id="div-intervalo" style="display:none;">
                    {!! Form::label('intervalo', 'Intervalo (días)', ['class' => 'pedido-field-label']) !!}
                    <div class="input-group pedido-input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-clock"></i></span></div>
                        {!! Form::number('intervalo', $pedido->intervalo ?? null, ['class' => 'form-control', 'min' => 1, 'id' => 'intervalo']) !!}
                    </div>
                </div>

                <div class="form-group col-xl-3 col-md-6" id="div-cantcuotas" style="display:none;">
                    {!! Form::label('cant_cuotas', 'Cantidad de cuotas', ['class' => 'pedido-field-label']) !!}
                    <div class="input-group pedido-input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-list-ol"></i></span></div>
                        {!! Form::number('cant_cuotas', $pedido->cant_cuotas ?? null, ['class' => 'form-control', 'min' => 1, 'id' => 'cant_cuotas']) !!}
                    </div>
                </div>

                <div class="form-group col-xl-6 col-12">
                    {!! Form::label('obs', 'Observación', ['class' => 'pedido-field-label']) !!}
                    <div class="input-group pedido-input-group pedido-textarea-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-comment-alt"></i></span></div>
                        {!! Form::textarea('obs', $pedido->obs ?? null, ['class' => 'form-control', 'placeholder' => 'Ingrese una observación opcional para el pedido', 'rows' => 2, 'style' => 'resize:none;']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-12 mt-3">@include('pedido_compras.detalle')</div>

<div class="col-12 mt-3">
    <div class="pedido-total-panel">
        <div class="pedido-total-info">
            <div class="pedido-total-icon"><i class="fas fa-calculator"></i></div>
            <div><span class="pedido-total-caption">Total del pedido</span><small>El total se actualiza automáticamente según cantidades y descuento.</small></div>
        </div>
        <div class="pedido-total-field">
            <span class="pedido-total-currency">Gs.</span>
            {!! Form::text('ped_total', isset($pedido) ? number_format($pedido->ped_total, 0, ',', '.') : null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'ped_total']) !!}
        </div>
    </div>
</div>

@include('pedido_compras.modal_producto')

<button id="btnScroll" type="button" class="btn pedido-scroll-btn" onclick="toggleScroll()" title="Ir al final"><i id="iconScroll" class="fas fa-arrow-down"></i></button>

<style>
.pedido-form-section{width:100%;overflow:hidden;border:1px solid #e4eaf1;border-radius:14px;background:#fff;box-shadow:0 8px 24px rgba(31,45,61,.05)}
.pedido-form-section-header{min-height:72px;padding:15px 18px;display:flex;align-items:center;gap:12px;border-bottom:1px solid #e8edf3;background:linear-gradient(180deg,#fff 0%,#fbfcfe 100%)}
.pedido-form-section-icon{width:42px;height:42px;flex:0 0 42px;display:inline-flex;align-items:center;justify-content:center;border-radius:11px;color:#2563eb;background:#edf4ff;font-size:16px}
.pedido-form-section-header h3{margin:0;color:#243447;font-size:15px;font-weight:700}.pedido-form-section-header p{margin:4px 0 0;color:#7b8797;font-size:12px}.pedido-form-section-body{padding:18px 18px 4px}
.pedido-field-label{margin-bottom:6px;color:#526173;font-size:11px;font-weight:700;letter-spacing:.025em;text-transform:uppercase}.pedido-field-help{display:block;margin-top:5px;color:#8a96a5;font-size:10.5px}
.pedido-input-group .input-group-text{min-width:42px;justify-content:center;border-color:#dce3eb;color:#668095;background:#f8fafc}.pedido-input-group .form-control,.pedido-select-shell .form-control{min-height:40px;border-color:#dce3eb;color:#344256;background:#fff;font-size:12px;box-shadow:none}.pedido-input-group .form-control:focus,.pedido-select-shell .form-control:focus{border-color:#80aaf8;box-shadow:0 0 0 2px rgba(37,99,235,.08)}.pedido-input-group .form-control[readonly],.pedido-input-group select:disabled{color:#657487;background:#f7f9fc}.pedido-textarea-group .input-group-text{align-items:flex-start;padding-top:11px}.pedido-textarea-group textarea.form-control{min-height:64px}
.pedido-select-shell{min-height:40px;display:flex;align-items:stretch}.pedido-select-icon{width:42px;flex:0 0 42px;display:inline-flex;align-items:center;justify-content:center;border:1px solid #dce3eb;border-right:0;border-radius:4px 0 0 4px;color:#668095;background:#f8fafc}.pedido-select-control{min-width:0;flex:1}.pedido-select-shell .select2-container{width:100%!important}.pedido-select-shell .select2-container .select2-selection--single{height:40px!important;border-color:#dce3eb!important;border-radius:0 4px 4px 0!important}.pedido-select-shell .select2-container .select2-selection--single .select2-selection__rendered{line-height:38px!important;padding-left:11px;padding-right:30px;color:#344256;font-size:12px}.pedido-select-shell .select2-container .select2-selection--single .select2-selection__arrow{height:38px!important}
.pedido-radio-group{min-height:40px;display:flex;gap:8px}.pedido-radio-option{min-width:78px;height:40px;margin:0;padding:0 12px;display:inline-flex;align-items:center;justify-content:center;gap:7px;border:1px solid #dce3eb;border-radius:8px;color:#5f6e80;background:#fff;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s ease}.pedido-radio-option:hover{border-color:#a9c4f7;background:#f8fbff}.pedido-radio-input{position:absolute;opacity:0;pointer-events:none}.pedido-radio-box{width:19px;height:19px;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;color:#8fa0b3;background:#eef2f6;font-size:8px}#descuento_si:checked+.pedido-radio-box{color:#fff;background:#16a34a}#descuento_no:checked+.pedido-radio-box{color:#fff;background:#64748b}
.pedido-total-panel{padding:16px 18px;display:flex;align-items:center;justify-content:space-between;gap:18px;border:1px solid #dce6f2;border-radius:14px;background:linear-gradient(135deg,#f8fbff 0%,#fff 65%);box-shadow:0 8px 20px rgba(31,45,61,.04)}.pedido-total-info{min-width:0;display:flex;align-items:center;gap:11px}.pedido-total-icon{width:40px;height:40px;flex:0 0 40px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px;color:#2563eb;background:#eaf2ff}.pedido-total-caption{display:block;color:#334155;font-size:13px;font-weight:700}.pedido-total-info small{display:block;margin-top:2px;color:#8491a1;font-size:10.5px}.pedido-total-field{width:min(320px,100%);display:flex;align-items:stretch}.pedido-total-currency{min-width:52px;display:inline-flex;align-items:center;justify-content:center;border:1px solid #bfd2ee;border-right:0;border-radius:9px 0 0 9px;color:#43658f;background:#edf4ff;font-size:12px;font-weight:700}.pedido-total-field .form-control{height:44px;border:1px solid #bfd2ee;border-radius:0 9px 9px 0;color:#164e87;background:#fff;font-size:18px;font-weight:800;text-align:right;box-shadow:none}
.pedido-scroll-btn{position:fixed;right:28px;bottom:26px;z-index:1030;width:46px;height:46px;padding:0;display:flex;align-items:center;justify-content:center;border:0;border-radius:50%;color:#fff;background:#2563eb;box-shadow:0 9px 22px rgba(37,99,235,.28);opacity:0;visibility:hidden;transform:translateY(12px);transition:all .2s ease}.pedido-scroll-btn.show{opacity:1;visibility:visible;transform:translateY(0)}.pedido-scroll-btn:hover{color:#fff;background:#1d4ed8}.toast-grande{width:min(450px,calc(100vw - 24px))!important;padding:14px 18px;font-size:16px}
@media(max-width:767.98px){.pedido-form-section-body{padding:15px 14px 2px}.pedido-total-panel{align-items:stretch;flex-direction:column}.pedido-total-field{width:100%}.pedido-scroll-btn{right:18px;bottom:18px}}
</style>

@push('page_scripts')
<script type="text/javascript">
let ES_EDIT={!! isset($pedido) ? 'true' : 'false' !!};let scrollMode='bottom';
$(document).ready(function(){
$('form.confirm-submit').on('keypress',function(e){if(e.which===13&&!$(e.target).is('textarea')){e.preventDefault();return false;}});
if(ES_EDIT){cargarDetalleEdit();let descuento=parseFloat("{{ $pedido->descuento ?? 0 }}")||0;if(descuento>0){$('#descuento_si').prop('checked',true);$('#descuento_no').prop('checked',false);$('#div-descuento').show();$('#descuento_input').val(descuento);}else{$('#descuento_no').prop('checked',true);$('#descuento_si').prop('checked',false);$('#div-descuento').hide();$('#descuento_input').val(0);}}
$('#productSearchModalPed').on('show.bs.modal',function(){fetchProductos($('#productSearchQueryPed').val()||'',$('#cod_suc').val());});
let timeout=null;$('#productSearchQueryPed').on('keyup',function(){clearTimeout(timeout);let query=$(this).val().trim(),cod_suc=$('#cod_suc').val();timeout=setTimeout(function(){if(query.length<4){$('#modalResultsPed').html('<div class="text-center text-muted p-4"><i class="fas fa-search mb-2 d-block"></i>Escriba al menos 4 caracteres...</div>');return;}fetchProductos(query,cod_suc);},700);});
$('#descuento_si, #descuento_no').on('change',toggleDescuento);$('#condicion').on('change',toggleCondicion);$('#descuento_input').on('keyup change',calcularTotal);$('#descuento_input').on('input',function(){let valor=parseFloat(this.value)||0,error=document.getElementById('error-descuento');if(valor>100){this.value=100;if(error)error.classList.remove('d-none');Swal.fire({icon:'warning',title:'Límite excedido',text:'El descuento máximo permitido es 100%',timer:1200,showConfirmButton:false,toast:true,position:'top-end'});}else if(error){error.classList.add('d-none');}if(valor<0)this.value=0;});toggleDescuento();toggleCondicion();calcularTotal();});
function fetchProductos(query,cod_suc){fetch('{{ url('buscar-productos-ped') }}?query='+encodeURIComponent(query||'')+'&cod_suc='+encodeURIComponent(cod_suc||''),{headers:{'X-Requested-With':'XMLHttpRequest'}}).then(function(res){if(!res.ok)throw new Error();return res.text();}).then(function(html){let c=document.getElementById('modalResultsPed');if(c)c.innerHTML=html;}).catch(function(){let c=document.getElementById('modalResultsPed');if(c)c.innerHTML='<div class="text-center text-danger p-4">No se pudieron cargar los productos.</div>';});}
function formatearMiles(numero){return Number(numero||0).toLocaleString('es-PY');}
function cargarDetalleEdit(){let detalle=@json($detalle ?? []),tabla=document.getElementById('selectedProducts');if(!tabla||!detalle.length)return;tabla.innerHTML='';detalle.forEach(function(item){let cantidad=parseInt(item.det_cantidad||0),precio=parseFloat(item.det_precio||0),subtotal=parseFloat(item.det_subtotal||(precio*cantidad)),row=document.createElement('tr');row.innerHTML=`<td class="text-center"><input name="codigo[]" value="${item.art_codigo}" readonly class="form-control form-control-sm text-center"></td><td><input name="producto[]" value="${item.art_descripcion}" readonly class="form-control form-control-sm"></td><td class="text-center"><input name="cantidad[]" value="${cantidad}" class="form-control form-control-sm text-center cantidad" min="1"></td><td class="text-center"><input type="hidden" class="precio_raw" value="${precio}"><input class="form-control form-control-sm text-right" value="${formatearMiles(precio)}" readonly></td><td class="text-center"><input class="form-control form-control-sm text-right subtotal" value="${formatearMiles(subtotal)}" data-value="${subtotal}" readonly></td><td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="confirmarBorrado(this)" title="Eliminar producto"><i class="far fa-trash-alt"></i></button></td>`;tabla.appendChild(row);});calcularTodo();}
function seleccionarProductoPed(codigo,producto,precio){let tabla=document.getElementById('selectedProducts'),cantidadInput=document.getElementById('cantidad_multiplicador'),cantidadMultiplicador=parseInt(cantidadInput?cantidadInput.value:1)||1;if(!tabla)return;let existe=Array.from(tabla.querySelectorAll("input[name='codigo[]']")).some(function(i){return i.value===codigo;});if(existe){Swal.fire({icon:'warning',title:'Producto ya agregado',text:'Solo podés modificar la cantidad en la tabla.',timer:1500,showConfirmButton:false,toast:true,position:'top-end',customClass:{popup:'toast-grande'}});return;}let subtotal=precio*cantidadMultiplicador,row=document.createElement('tr');row.innerHTML=`<td class="text-center"><input name="codigo[]" value="${codigo}" readonly class="form-control form-control-sm text-center"></td><td><input name="producto[]" value="${producto}" readonly class="form-control form-control-sm"></td><td class="text-center"><input name="cantidad[]" value="${cantidadMultiplicador}" class="form-control form-control-sm text-center cantidad" min="1"></td><td class="text-center"><input type="hidden" class="precio_raw" value="${precio}"><input value="${formatearMiles(precio)}" readonly class="form-control form-control-sm text-right"></td><td class="text-center"><input class="form-control form-control-sm text-right subtotal" value="${formatearMiles(subtotal)}" data-value="${subtotal}" readonly></td><td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="borrarFila(this)" title="Eliminar producto"><i class="far fa-trash-alt"></i></button></td>`;tabla.appendChild(row);row.scrollIntoView({behavior:'smooth',block:'center'});row.style.backgroundColor='#eefbf2';setTimeout(function(){row.style.transition='background-color .5s';row.style.backgroundColor='';},800);Swal.fire({icon:'success',title:'Agregado',timer:800,toast:true,position:'top-end',showConfirmButton:false,customClass:{popup:'toast-grande'}});calcularTodo();}
function recalcularFila(row){let c=row.querySelector('.cantidad'),p=row.querySelector('.precio_raw'),s=row.querySelector('.subtotal');if(!c||!p||!s)return;let cantidad=parseInt(c.value)||0,precio=parseFloat(p.value)||0;if(cantidad<1){cantidad=1;c.value=1;}let subtotal=cantidad*precio;s.dataset.value=subtotal;s.value=formatearMiles(subtotal);}
function calcularTodo(){let totalCantidad=0;document.querySelectorAll('#selectedProducts tr').forEach(function(row){let input=row.querySelector('.cantidad');if(!input)return;let cantidad=parseInt(input.value)||0;if(cantidad<1){input.value=1;cantidad=1;}totalCantidad+=cantidad;recalcularFila(row);});let e=document.getElementById('totalCantidad');if(e)e.innerText=totalCantidad;let m=document.getElementById('modalCantidadProductos');if(m)m.innerText=totalCantidad;calcularTotal();}
function calcularTotal(){let total=0;document.querySelectorAll('.subtotal').forEach(function(i){total+=parseFloat(i.dataset.value||0);});if($('#descuento_si').is(':checked')){let d=parseFloat($('#descuento_input').val())||0;total-=total*(d/100);}let p=document.getElementById('ped_total');if(p)p.value=formatearMiles(total);let m=document.getElementById('modalTotalPedido');if(m)m.innerText=formatearMiles(total);}
function borrarFila(btn){let row=btn?btn.closest('tr'):null;if(row)row.remove();calcularTodo();}
function toggleDescuento(){if($('#descuento_si').is(':checked')){$('#div-descuento').show();}else{$('#div-descuento').hide();$('#descuento_input').val(0);}calcularTotal();}
function toggleCondicion(){if($('#condicion').val()==='CREDITO'){$('#div-intervalo, #div-cantcuotas').show();}else{$('#div-intervalo, #div-cantcuotas').hide();}}
document.addEventListener('input',function(e){if(e.target.classList.contains('cantidad')){e.target.value=e.target.value.replace(/[^0-9]/g,'');calcularTodo();}});
function toggleScroll(){if(scrollMode==='top'){window.scrollTo({top:0,behavior:'smooth'});}else{window.scrollTo({top:document.documentElement.scrollHeight,behavior:'smooth'});}}
window.addEventListener('scroll',function(){let btn=document.getElementById('btnScroll'),icon=document.getElementById('iconScroll');if(!btn||!icon)return;let scrollTop=window.scrollY,docHeight=document.documentElement.scrollHeight,windowHeight=window.innerHeight;if(scrollTop>200)btn.classList.add('show');else btn.classList.remove('show');if(scrollTop+windowHeight>=docHeight-50){scrollMode='top';btn.title='Volver arriba';icon.classList.remove('fa-arrow-down');icon.classList.add('fa-arrow-up');}else{scrollMode='bottom';btn.title='Ir al final';icon.classList.remove('fa-arrow-up');icon.classList.add('fa-arrow-down');}});
function confirmarBorrado(btn){Swal.fire({title:'¿Eliminar producto?',text:'Esta acción quitará el producto del detalle.',icon:'warning',showCancelButton:true,confirmButtonColor:'#d33',cancelButtonColor:'#6c757d',confirmButtonText:'Sí, eliminar',cancelButtonText:'Cancelar'}).then(function(result){if(result.isConfirmed){borrarFila(btn);Swal.fire({icon:'success',title:'Eliminado',timer:800,showConfirmButton:false,toast:true,position:'top-end',customClass:{popup:'toast-grande'}});}});}
</script>
@endpush
