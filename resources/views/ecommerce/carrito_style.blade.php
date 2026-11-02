<style>
/* Carrito y selección de variantes, aislados del resto del ERP. */
.ec-header-cart{display:inline-flex;align-items:center;gap:8px;min-height:43px;padding:9px 13px;background:#182231;color:#fff;border:1px solid #182231;border-radius:5px;font-weight:800;font-size:12px;white-space:nowrap}
.ec-header-cart:hover,.ec-header-cart:focus{background:#303b4c;color:#fff}
.ec-header-cart i{font-size:14px}
.ec-cart-count{background:#e53b48;color:#fff;border-radius:40px;min-width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;padding:0 5px;font-size:10px;font-weight:900}
.ec-card-actions{display:flex;flex-direction:column;gap:8px}
.ec-add-cart-card{width:100%;padding:12px 9px;border:0;border-radius:4px;background:#e53b48;color:#fff;font-weight:850;font-size:11px;letter-spacing:.1px}
.ec-add-cart-card:hover{background:#c82d3b;color:#fff}
.ec-add-cart-card i{margin-right:7px}
.ec-product-dialog .ec-detail-variants{display:flex;flex-direction:column;gap:8px;max-height:285px;overflow:auto}
.ec-product-dialog .ec-detail-variant{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 11px;border:1px solid #e5e9ef;border-radius:5px}
.ec-product-dialog .ec-variant-main{min-width:0;display:flex;flex-direction:column;gap:3px}
.ec-product-dialog .ec-variant-main b{font-size:12px;color:#263244}
.ec-product-dialog .ec-variant-main span{font-size:10px;color:#728096;word-break:break-word;text-align:left}
.ec-product-dialog .ec-variant-main small{font-size:10px;color:#57806c}
.ec-variant-order{display:flex;align-items:center;gap:6px;flex:0 0 auto}
.ec-variant-order label{font-size:10px;color:#6e7989;margin:0}
.ec-variant-order input{width:61px;height:37px;border:1px solid #d6dde6;border-radius:4px;padding:6px;font-size:12px;text-align:center}
.ec-variant-order button{height:37px;padding:6px 10px;border:none;background:#e53b48;color:#fff;border-radius:4px;font-size:11px;font-weight:800;white-space:nowrap}
.ec-variant-order button:hover{background:#cc3040}
.ec-variant-order button:disabled{opacity:.6;cursor:progress}
.ec-detail-cart-actions{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:8px;border-top:1px solid #eceff4;padding:14px 0;margin-top:8px}
.ec-detail-cart-actions>span{font-size:10px;color:#768392;max-width:180px}
.ec-detail-cart-actions button{border:1px solid #182434;border-radius:4px;background:#182434;color:#fff;padding:10px 12px;font-size:11px;font-weight:850}
.ec-detail-cart-actions button:hover{background:#314154}
.ec-cart-overlay{position:fixed;inset:0;background:rgba(13,20,31,.54);z-index:2199}
.ec-cart-overlay[hidden]{display:none!important}
.ec-cart-drawer{position:fixed;top:0;right:0;bottom:0;width:min(440px,100vw);max-width:100vw;background:#fff;z-index:2200;transform:translateX(105%);box-shadow:-12px 0 44px rgba(0,0,0,.18);transition:transform .24s ease;display:flex;flex-direction:column;visibility:hidden}
.ec-cart-drawer.ec-cart-open{visibility:visible;transform:translateX(0)}
.ec-cart-drawer-head{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:22px 23px;border-bottom:1px solid #e9edf2}
.ec-cart-drawer-head small{font-size:9px;letter-spacing:1.5px;font-weight:900;color:#dc3545}
.ec-cart-drawer-head h2{font-size:20px;font-weight:950;color:#192535;margin:4px 0 0}
.ec-cart-drawer-head h2 i{color:#e53b48;margin-right:7px}
.ec-cart-close{background:#f2f4f7;border:0;border-radius:4px;width:33px;height:33px;color:#4b5563}
.ec-cart-close:hover{background:#e9edf1}
.ec-cart-drawer-desc{font-size:11px;line-height:1.6;color:#778395;padding:13px 23px;border-bottom:1px solid #ebedf2}
.ec-cart-feedback{margin:12px 23px 0;background:#fff2f3;border:1px solid #f4cfd3;color:#a8323d;padding:10px 12px;font-size:12px;border-radius:5px}
.ec-cart-feedback[hidden]{display:none!important}
.ec-cart-items{padding:12px 18px;overflow:auto;flex:1;min-height:0}
.ec-cart-loading{padding:25px 10px;text-align:center;font-size:12px;color:#8993a2}
.ec-cart-empty{text-align:center;padding:54px 18px;font-size:12px;color:#8c99ab}
.ec-cart-empty i{display:block;font-size:45px;color:#d1d7e1;margin-bottom:16px}
.ec-cart-empty strong{display:block;color:#273548;font-size:15px;margin-bottom:6px}
.ec-cart-line{display:flex;gap:12px;padding:16px 5px;border-bottom:1px solid #ecf0f4}
.ec-cart-line-icon{flex:0 0 55px;height:60px;background:#f4f6f9;border-radius:5px;display:flex;align-items:center;justify-content:center;color:#acb7c5}
.ec-cart-line-info{flex:1;min-width:0}
.ec-cart-line-info h3{font-size:12px;color:#1f2b3a;font-weight:900;line-height:1.4;margin:0 0 3px}
.ec-cart-line-info small{font-size:10px;color:#8794a5;display:block;word-break:break-word}
.ec-cart-line-meta{display:flex;justify-content:space-between;gap:8px;align-items:center;margin-top:8px}
.ec-cart-line-meta strong{font-size:13px;color:#242f40}
.ec-cart-controls{display:inline-flex;align-items:center;border:1px solid #dfe5eb;border-radius:4px;overflow:hidden}
.ec-cart-controls button{border:0;background:#f9fafb;color:#253448;width:29px;height:29px;font-size:11px}
.ec-cart-controls button:hover{background:#ecf1f8}
.ec-cart-controls input{width:49px;height:29px;border:0;text-align:center;font-size:12px;color:#263345;outline:0}
.ec-cart-remove{border:0;background:transparent;color:#b55460;cursor:pointer;font-size:10px;margin-top:8px;padding:0}
.ec-cart-remove:hover{text-decoration:underline}
.ec-cart-drawer-foot{flex:0 0 auto;border-top:1px solid #e8ecf1;padding:17px 23px 23px;background:#fff}
.ec-cart-total-row{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:10px;font-size:12px;color:#6c7a8c}
.ec-cart-total-row strong{color:#202c3b;font-size:14px}
.ec-cart-amount{padding-top:13px;border-top:1px solid #ecf0f5}
.ec-cart-amount strong{font-size:21px;font-weight:950}
.ec-cart-drawer-foot p{color:#939dad;font-size:10px;line-height:1.5;margin:0 0 12px}
.ec-cart-continue{width:100%;min-height:46px;display:flex;align-items:center;justify-content:center;gap:10px;color:#fff;background:#e53b48;border:0;border-radius:4px;font-size:12px;font-weight:850}
.ec-cart-continue:hover:not(:disabled){background:#cd2939}
.ec-cart-continue:disabled{background:#adb5bf;cursor:not-allowed}
.ec-cart-secondary-actions{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:16px}
.ec-cart-secondary-actions button{padding:6px 0;border:none;background:transparent;font-size:11px;font-weight:750;color:#5c6878}
.ec-cart-secondary-actions .ec-cart-clear{color:#ac5260}
@media(max-width:550px){.ec-cart-drawer-head{padding:15px 17px}.ec-cart-drawer-desc{padding:12px 17px}.ec-cart-drawer-foot{padding:15px 17px 23px}.ec-cart-items{padding:8px 12px}.ec-product-dialog .ec-detail-variant{flex-wrap:wrap}.ec-variant-order{width:100%;justify-content:flex-end}}
@media(prefers-reduced-motion:reduce){.ec-cart-drawer{transition:none}}
</style>