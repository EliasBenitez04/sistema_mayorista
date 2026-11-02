<style>
/* Carga rápida E-commerce: cantidad global + talle a un clic, como el buscador de pedidos. */
.ec-storefront .ec-shop-body{padding-bottom:110px}
.ec-fastbar{position:sticky;top:7px;z-index:24;display:flex;align-items:center;flex-wrap:wrap;gap:14px;justify-content:space-between;background:#192231;color:#fff;border:1px solid #354154;border-radius:8px;padding:14px 17px;margin:0 0 22px;box-shadow:0 8px 23px rgba(16,27,43,.15)}
.ec-fastbar-title{display:flex;align-items:center;gap:10px;min-width:170px}
.ec-fastbar-icon{width:37px;height:37px;border-radius:7px;background:#e53b48;color:#fff;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.ec-fastbar-title strong{display:block;color:#fff;font-size:13px;font-weight:900;white-space:nowrap}
.ec-fastbar-title small{display:block;color:#b7c4d4;font-size:10px;line-height:1.4;max-width:205px;margin-top:2px}
.ec-fastbar-controls{display:flex;align-items:center;gap:9px;flex-wrap:wrap}
.ec-fastbar-controls>label{font-size:11px!important;color:#dbe3ef!important;font-weight:750!important;margin:0 4px 0 0!important;white-space:nowrap}
.ec-fastbar-stepper{display:flex;align-items:center;background:#fff;border-radius:5px;overflow:hidden;border:1px solid #e6ebf3;height:40px}
.ec-fastbar-stepper button{width:34px;height:40px;background:#f5f7fa;border:0;color:#233042;font-size:12px}
.ec-fastbar-stepper button:hover{background:#e4eaf2}
.ec-fastbar-stepper input{width:59px;height:40px;border:0!important;box-shadow:none!important;text-align:center;font-size:15px;color:#162536;font-weight:900;outline:0;padding:2px}
.ec-fastbar-presets{display:flex;gap:5px;align-items:center}
.ec-fastbar-presets button{background:transparent;color:#cdd7e5;border:1px solid #647084;border-radius:4px;min-width:33px;height:35px;padding:6px;font-size:11px;font-weight:800}
.ec-fastbar-presets button:hover,.ec-fastbar-presets .is-active{background:#e53b48;border-color:#e53b48;color:#fff}
.ec-fastbar-cart{background:#fff;color:#263448;border:0;border-radius:4px;min-height:40px;padding:8px 13px;font-size:11px;font-weight:900;display:inline-flex;align-items:center;gap:8px;white-space:nowrap}
.ec-fastbar-cart strong{color:#fff;background:#e53b48;padding:2px 7px;min-width:22px;border-radius:20px}
.ec-card-sizes.ec-card-sizes-quick{display:block;margin:9px 0 14px;min-height:0}
.ec-sizes-heading{display:flex;align-items:center;justify-content:space-between;gap:8px;margin:0 0 9px}
.ec-sizes-heading strong{font-size:10px;font-weight:950;letter-spacing:.55px;color:#394658;text-transform:uppercase}
.ec-sizes-heading span{font-size:9px;color:#8b96a6;white-space:nowrap}
.ec-sizes-heading i{color:#d9424e}
.ec-sizes-buttons{display:flex;align-items:stretch;flex-wrap:wrap;gap:6px}
.ec-storefront .ec-sizes-buttons .ec-quick-size{position:relative;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:3px;min-width:52px;min-height:48px;border:1px solid #dce2eb;background:#f9fafc;color:#243449;border-radius:5px;padding:6px 5px;transition:background .13s,border-color .13s,transform .13s;box-shadow:none;text-decoration:none;flex:1 0 20%;max-width:calc(25% - 5px);overflow:visible}
.ec-sizes-buttons button.ec-quick-size:hover,.ec-sizes-buttons button.ec-quick-size:focus-visible{background:#ffecef;border-color:#db3d4a;color:#bd2434;outline:2px solid rgba(219,61,74,.17);outline-offset:0}
.ec-sizes-buttons .ec-quick-size>span{font-size:12px;font-weight:950;line-height:1}
.ec-sizes-buttons .ec-quick-size>small{font-size:9px;color:#8e99a7;line-height:1}
.ec-sizes-buttons .ec-quick-size.is-unavailable{background:#f6f7f9;color:#a1aab7;border-color:#eef0f4;opacity:.7;cursor:not-allowed}
.ec-sizes-buttons .ec-quick-size.is-unavailable>small{color:#a6afbc}
.ec-sizes-buttons .ec-quick-size.ec-size-selected{border-color:#39a17f;background:#ecf9f4;color:#236a54}
.ec-sizes-buttons .ec-quick-size.ec-size-selected>small{color:#3e896f}
.ec-sizes-buttons .ec-quick-size.ec-size-requesting{border-color:#e39c4b;background:#fff8e9}
.ec-sizes-buttons .ec-size-in-cart{font-size:9px;font-weight:900;font-style:normal;color:#fff;background:#138b65;min-width:17px;height:17px;border-radius:40px;display:flex;align-items:center;justify-content:center;padding:0 3px;position:absolute;right:-4px;top:-6px}
.ec-sizes-buttons .ec-size-in-cart[hidden]{display:none!important}
.ec-sizes-buttons .ec-quick-more{min-height:46px;border:1px dashed #ced4df;color:#647087;background:#fff;border-radius:5px;font-size:11px;font-weight:800;min-width:55px}
.ec-sizes-buttons .ec-quick-more:hover{background:#e9eef5}
.ec-quick-dock{position:fixed;right:20px;bottom:17px;z-index:140;display:flex;align-items:center;gap:11px;border:1px solid #dfe4eb;border-radius:9px;background:#fff;box-shadow:0 12px 45px rgba(17,30,47,.23);padding:10px 12px 10px 13px;max-width:calc(100vw - 28px)}
.ec-quick-dock-info{display:flex;align-items:center;gap:10px;min-width:115px}
.ec-quick-dock-info>i{background:#fff0f1;color:#d73f4e;display:flex;align-items:center;justify-content:center;border-radius:6px;width:34px;height:34px;font-size:15px}
.ec-quick-dock-info strong{display:block;white-space:nowrap;font-size:12px;font-weight:950;color:#1b293b}
.ec-quick-dock-info small{display:block;white-space:nowrap;font-size:10px;color:#6b7786;margin-top:2px}
.ec-quick-review{border:1px solid #d8dfe8;border-radius:4px;padding:10px 12px;background:#f6f8fa;color:#263548;font-size:11px;font-weight:800}
.ec-quick-review:hover{background:#e9edf4}
.ec-quick-continue-form{margin:0}
.ec-quick-continue{display:flex;align-items:center;justify-content:center;gap:10px;background:#e53b48;color:#fff;padding:12px 15px;min-height:41px;border-radius:4px;border:0;font-size:11px;font-weight:900;white-space:nowrap}
.ec-quick-continue:not(:disabled):hover{background:#c52a3a}
.ec-quick-continue:disabled{background:#aab4c0;color:#fff;cursor:not-allowed}
.ec-quick-toast{position:fixed;left:50%;bottom:95px;transform:translateX(-50%);max-width:min(500px,calc(100vw - 22px));background:#192637;color:#fff;padding:13px 17px;border-radius:7px;z-index:2205;box-shadow:0 9px 28px rgba(0,0,0,.2);font-size:12px;font-weight:750;text-align:center}
.ec-quick-toast.is-error{background:#ad2635}
.ec-quick-toast[hidden]{display:none!important}
.ec-storefront .ec-card-information{gap:0}
.ec-storefront .ec-view-product{padding:10px 7px;font-size:10px;color:#43536a;border-color:#e0e5ec}
.ec-storefront .ec-view-product:hover{color:#fff}
@media(max-width:1250px){.ec-fastbar-title small{max-width:160px}.ec-fastbar{gap:8px;padding:12px}.ec-fastbar-controls>label{display:none}}
@media(max-width:1100px){.ec-fastbar{top:0}.ec-fastbar-title{min-width:160px}.ec-fastbar-cart{min-width:39px}.ec-fastbar-cart>span{display:none}}
@media(max-width:650px){.ec-fastbar{margin-bottom:14px;gap:9px}.ec-fastbar-title{width:100%}.ec-fastbar-title small{max-width:none}.ec-fastbar-controls{flex:1}.ec-fastbar-controls>label{display:none}.ec-fastbar-presets button{min-width:27px}.ec-quick-dock{left:7px;right:7px;bottom:7px;gap:5px;justify-content:space-between;padding:8px}.ec-quick-dock-info{gap:5px;min-width:0}.ec-quick-dock-info>i{display:none}.ec-quick-dock-info strong{font-size:10px}.ec-quick-dock-info small{font-size:9px}.ec-quick-review{padding:10px 8px;font-size:10px}.ec-quick-continue{padding:10px 7px;font-size:10px;min-height:39px}.ec-quick-dock .ec-quick-continue i{display:none}.ec-quick-toast{bottom:75px}.ec-fastbar-presets{gap:3px}}
</style>