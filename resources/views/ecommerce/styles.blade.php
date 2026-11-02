<style>
/* E-commerce: estilos aislados, sin afectar pedidos ni pantallas administrativas. */
.ec-storefront{--ec-accent:#e53b48;--ec-dark:#141922;--ec-text:#192232;--ec-muted:#768091;--ec-border:#e8eaf0;--ec-bg:#f7f8fa;background:#fff;color:var(--ec-text);font-family:Roboto,"Segoe UI",Arial,sans-serif;min-height:calc(100vh - 58px);-webkit-font-smoothing:antialiased}
.ec-storefront *, .ec-product-dialog *{box-sizing:border-box}
.ec-storefront a{text-decoration:none}.ec-storefront button{cursor:pointer}
.ec-service-bar{min-height:35px;padding:8px clamp(20px,3vw,54px);background:#141922;color:#fff;display:flex;justify-content:space-between;align-items:center;gap:20px;font-size:10px;font-weight:700;letter-spacing:.7px}
.ec-service-bar .ec-service-dot{color:#65c5a1;font-size:7px;margin-right:6px}.ec-service-note{font-weight:400;color:#b5c0cd;letter-spacing:0}.ec-back-admin{color:#f2f3f6!important;white-space:nowrap}
.ec-back-admin:hover{color:#fff!important;text-decoration:underline}
.ec-shop-header{display:flex;align-items:center;gap:clamp(12px,2vw,32px);padding:20px clamp(20px,3vw,54px);min-height:99px;max-width:2000px;margin:auto}
.ec-brand{display:flex;align-items:center;gap:10px;flex:0 0 auto;color:#18202d!important}
.ec-brand-mark{display:flex;align-items:center;justify-content:center;background:var(--ec-accent);border-radius:7px;transform:skew(-8deg);color:#fff;font-size:30px;font-weight:950;letter-spacing:-2px;width:51px;height:49px}
.ec-brand-mark span{color:#192334}
.ec-brand-word{font-weight:950;font-size:21px;letter-spacing:-1px;line-height:1.05}
.ec-brand-word small{display:block;color:#89909c;letter-spacing:1.9px;font-weight:750;font-size:8px;margin-top:5px}
.ec-search-form{display:flex;align-items:stretch;height:47px;max-width:660px;flex:1;margin:auto;border:1px solid #dfe3eb;border-radius:6px;overflow:hidden;background:#fafbfc;transition:border-color .2s,box-shadow .2s}
.ec-search-form:focus-within{border-color:#d24a52;box-shadow:0 0 0 3px rgba(229,59,72,.09)}
.ec-search-form input{flex:1;width:30px;min-width:0;background:transparent;outline:0;border:none!important;box-shadow:none!important;padding:0 15px;font-size:13px;color:#242d3a}
.ec-search-form input:focus{box-shadow:none!important;background:transparent!important}
.ec-search-form button{min-width:102px;display:flex;align-items:center;justify-content:center;gap:9px;background:var(--ec-accent);border:0;color:white;font-size:12px;font-weight:800}
.ec-search-form button:hover{background:#d92e3c}
.ec-header-actions{display:flex;align-items:center;gap:10px}
.ec-header-actions a,.ec-header-actions button{display:inline-flex;align-items:center;justify-content:center;gap:9px;border-radius:5px;min-height:43px;padding:10px 12px;font-weight:750;font-size:11px;white-space:nowrap}
.ec-header-import{color:#202936!important;background:#fff;border:1px solid #dfe3eb}
.ec-header-import:hover{color:#d62c3d!important;border-color:#cdd2dc}
.ec-header-screen{border:1px solid #dfe3eb;background:#fff;color:#202936}
.ec-header-screen:hover{background:#f6f7f9}
.ec-category-bar{display:flex;align-items:stretch;gap:20px;border-top:1px solid #edf0f3;border-bottom:1px solid #e5e9ef;box-shadow:0 2px 8px rgba(0,0,0,.028);padding:0 clamp(20px,3vw,54px);min-height:50px}
.ec-category-label{display:inline-flex;align-items:center;gap:12px;color:#161e2d;font-weight:900;letter-spacing:1.2px;font-size:10px;flex:0 0 auto;white-space:nowrap}
.ec-category-label i{color:#b3bac5;font-size:9px}
.ec-category-scroll{display:flex;align-items:stretch;gap:2px;overflow:auto;scrollbar-width:thin;max-width:100%;-webkit-overflow-scrolling:touch}
.ec-category-link{display:flex;align-items:center;white-space:nowrap;padding:0 16px;border-bottom:3px solid transparent;color:#5d6675!important;font-size:11px;font-weight:750;transition:color .2s,border-color .2s}
.ec-category-link:hover{color:#db3340!important}.ec-category-link.is-current{color:#dc3342!important;border-bottom-color:var(--ec-accent)}
.ec-hero{background:#1c2230;min-height:235px;position:relative;display:flex;justify-content:space-between;overflow:hidden;margin:26px clamp(20px,3vw,54px) 0;border-radius:8px}
.ec-hero:after{content:"";background:linear-gradient(95deg,#161d29,transparent);position:absolute;inset:0 30% 0 0;pointer-events:none}
.ec-hero-copy{position:relative;z-index:2;padding:clamp(26px,3vw,45px) clamp(28px,4vw,68px);max-width:650px}
.ec-hero-eyebrow{display:flex;align-items:center;gap:9px;font-size:10px;font-weight:850;letter-spacing:1.7px;color:#ff9ea3}
.ec-hero-eyebrow span{height:7px;width:7px;background:#f45f6b;border-radius:50%}
.ec-hero h1{font-size:clamp(27px,3vw,45px)!important;font-weight:950;line-height:1.15;letter-spacing:-1.4px;color:#fff!important;margin:13px 0 10px!important}
.ec-hero h1 em{font-style:normal;color:#ff6d77}
.ec-hero p{font-size:13px;color:#c5cfdb;max-width:430px;line-height:1.6;margin:0 0 22px}
.ec-hero-btn{background:var(--ec-accent);color:white!important;padding:12px 18px;display:inline-flex;align-items:center;gap:24px;border-radius:4px;font-size:12px;font-weight:800}
.ec-hero-btn:hover{background:#cb2939}
.ec-hero-aside{position:absolute;right:0;top:0;bottom:0;width:48%;overflow:hidden;background:radial-gradient(circle at 74% 24%,#525060 0,#252938 53%,#202433 100%)}
.ec-hero-figure{position:absolute;border:1px solid rgba(255,255,255,.07);transform:rotate(28deg)}
.ec-figure-one{height:420px;width:400px;background:linear-gradient(150deg,rgba(237,74,91,.72),rgba(75,49,69,.15));border-radius:42px;right:9%;top:-83%}
.ec-figure-two{height:360px;width:210px;background:linear-gradient(130deg,rgba(255,255,255,.07),rgba(231,50,66,.23));border-radius:36px;right:-5%;bottom:-96%}
.ec-hero-initial{position:absolute;right:18%;top:13%;font-weight:950;font-style:italic;font-size:145px;line-height:1;color:rgba(255,255,255,.14);transform:rotate(-14deg);letter-spacing:-10px}
.ec-hero-tag{position:absolute;right:8%;bottom:20px;display:flex;flex-direction:column;align-items:flex-end;color:#c9c6d0;letter-spacing:2px;font-size:10px;font-weight:850}
.ec-hero-tag strong{font-size:18px;color:white;letter-spacing:3px}
.ec-info-strip{padding:16px clamp(20px,3vw,54px);display:flex;justify-content:space-between;gap:18px;border-bottom:1px solid #eef0f4}
.ec-info-strip span{display:flex;align-items:center;gap:11px;white-space:nowrap;color:#697482;font-size:11px}
.ec-info-strip i{color:#e1404e;font-size:15px}
.ec-shop-body{padding:20px clamp(20px,3vw,54px) 50px;background:#fff;max-width:2000px;margin:auto}
.ec-breadcrumb{display:flex;align-items:center;gap:10px;font-size:11px;color:#88909c;margin-bottom:24px}
.ec-breadcrumb a{color:#89929e!important}.ec-breadcrumb i{font-size:8px;color:#b9c0cb}.ec-breadcrumb span{color:#222a38;font-weight:750}
.ec-success{display:flex;align-items:center;gap:8px;background:#edf9f4;color:#187b55;border:1px solid #d6eddf;border-radius:6px;padding:12px 15px;font-size:12px;margin-bottom:20px}
.ec-shop-columns{display:grid;grid-template-columns:minmax(205px,236px) minmax(0,1fr);gap:29px}
.ec-filters{border:1px solid #e8ebef;border-radius:7px;align-self:start;padding:21px;background:#fff}
.ec-filters-heading{display:flex;align-items:center;justify-content:space-between;padding-bottom:19px;border-bottom:1px solid #eceef2}
.ec-filters-heading strong{font-size:15px}.ec-filters-heading i{font-size:15px;color:#99a1ac}
.ec-filter-group{padding:19px 0;border-bottom:1px solid #eceef2}
.ec-filter-title{display:block;color:#2a303a!important;font-size:11px!important;font-weight:900!important;letter-spacing:1px;margin:0 0 13px}
.ec-filter-input{width:100%;border-radius:4px;background:#fff;border:1px solid #dde2e9;padding:10px;font-size:12px;color:#445064;min-height:42px}
.ec-radio{display:flex;align-items:center;gap:10px;font-weight:450!important;color:#626b79!important;font-size:12px!important;line-height:1.4;margin-bottom:13px;cursor:pointer}
.ec-radio:last-child{margin-bottom:0}.ec-radio input{accent-color:#e23b4a}
.ec-price-range{display:flex;flex-direction:column;gap:11px}
.ec-price-range label{display:block;font-size:10px!important;color:#87919f!important;margin:0 0 5px}
.ec-price-range input{width:100%;min-width:0;padding:10px 12px;border:1px solid #dfe3eb;border-radius:4px;min-height:40px;font-size:12px}
.ec-apply-filters{margin-top:19px;background:var(--ec-dark);color:white;border:none;border-radius:4px;width:100%;display:flex;align-items:center;justify-content:center;gap:16px;font-size:12px;font-weight:800;padding:12px}
.ec-apply-filters:hover{background:#323b4b}
.ec-clear-filters{color:#7a8390!important;display:block;text-align:center;margin-top:14px;font-size:11px}
.ec-clear-filters:hover{color:#ce3040!important}
.ec-side-note{display:flex;align-items:flex-start;gap:9px;background:#f7f8fa;color:#7a8390;font-size:11px;line-height:1.5;border-radius:6px;padding:13px;margin-top:20px}
.ec-side-note i{color:#dd4654;margin-top:2px}
.ec-listing-head{display:flex;justify-content:space-between;align-items:center;gap:16px;margin:0 0 18px}
.ec-listing-eyebrow{font-size:10px;letter-spacing:1.7px;color:#df4150;font-weight:850}
.ec-listing-head h2{font-size:clamp(23px,2.3vw,30px)!important;font-weight:950;letter-spacing:-.7px;color:#1c2431;margin:5px 0!important}
.ec-listing-head p{font-size:12px;color:#7c8591;margin:0}
.ec-listing-head p strong{color:#232c39}
.ec-mobile-filter-btn{display:none;border:1px solid #dfe3eb;background:#fff;border-radius:4px;padding:10px 13px;color:#202936;font-size:12px;font-weight:750;align-items:center;gap:7px}
.ec-active-filters{display:flex;align-items:center;gap:7px;flex-wrap:wrap;padding:10px 0 18px;font-size:10px;color:#7d8998}
.ec-active-chip{padding:6px 9px;background:#f5f6f9;border:1px solid #e6eaf1;color:#424b5d;border-radius:4px}
.ec-active-filters a{color:#cf3544!important;font-weight:700;margin-left:auto}
.ec-products-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:21px}
.ec-product-card{min-width:0;border:1px solid #e9ecf0;background:#fff;border-radius:6px;overflow:hidden;transition:box-shadow .2s,transform .2s}
.ec-product-card:hover{box-shadow:0 13px 32px rgba(27,38,57,.10);transform:translateY(-2px)}
.ec-image-surface{position:relative;background:#f8f9fa;aspect-ratio:1/1;overflow:hidden;display:flex;align-items:center;justify-content:center}
.ec-image-open{position:absolute;inset:0;width:100%;height:100%;border:0;padding:0;background:transparent;z-index:1}
.ec-product-image{width:100%;height:100%;object-fit:contain;padding:10px;transition:transform .28s ease}
.ec-product-card:hover .ec-product-image{transform:scale(1.035)}
.ec-image-placeholder,.ec-detail-fallback{display:none;flex-direction:column;align-items:center;justify-content:center;gap:11px;color:#a4adba;position:absolute;inset:0;font-weight:700;font-size:11px;background:linear-gradient(145deg,#f7f8fb,#eff1f5)}
.ec-image-placeholder i,.ec-detail-fallback i{font-size:56px;color:#d8dee7}
.ec-image-failed .ec-image-placeholder,.ec-image-failed .ec-detail-fallback{display:flex}
.ec-image-failed .ec-product-image,.ec-detail-image.ec-image-failed>img{display:none}
.ec-image-failed .ec-image-open{pointer-events:none}
.ec-photo-label{position:absolute;top:12px;left:12px;max-width:calc(100% - 24px);text-overflow:ellipsis;overflow:hidden;white-space:nowrap;background:#fff;color:#1f2a39;font-size:9px;letter-spacing:.7px;font-weight:900;padding:7px 10px;border-radius:3px;z-index:2;text-transform:uppercase}
.ec-stock-badge{position:absolute;bottom:11px;left:11px;background:rgba(255,255,255,.97);border-radius:3px;color:#24815f;font-size:9px;padding:7px 9px;font-weight:850;z-index:2}
.ec-stock-badge i{font-size:6px;vertical-align:middle;margin-right:4px}
.ec-stock-badge.ec-stock-empty{color:#a06a34}
.ec-quick-icon{position:absolute;right:11px;bottom:11px;display:flex;align-items:center;justify-content:center;width:34px;height:34px;border:1px solid #e9e9ee;background:#fff;color:#232e3c;border-radius:4px;z-index:3;transition:background .15s,color .15s}
.ec-quick-icon:hover{background:#e53b48;color:white}
.ec-card-information{padding:17px 16px 14px;display:flex;flex-direction:column}
.ec-card-category{font-size:10px;color:#db3d4b;text-transform:uppercase;letter-spacing:1px;font-weight:850;line-height:1.4;margin-bottom:8px}
.ec-card-information h3{font-size:14px!important;font-weight:850;line-height:1.42;color:#202b3b;margin:0 0 7px!important;min-height:40px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.ec-product-sku{color:#929baa;font-size:10px;word-break:break-word}
.ec-card-price{margin:14px 0 12px;display:flex;flex-direction:column;gap:4px}
.ec-card-price strong{font-size:19px;font-weight:950;letter-spacing:-.3px;color:#131d2c}
.ec-card-price small{color:#7a8493;font-size:11px}
.ec-card-sizes{display:flex;align-items:center;gap:5px;flex-wrap:wrap;min-height:32px;margin-top:auto;margin-bottom:14px}
.ec-card-sizes>span:first-child{font-size:10px;color:#808b9a;font-weight:750;margin-right:4px}
.ec-card-sizes>span:not(:first-child){font-size:10px;min-width:28px;text-align:center;color:#303e50;border:1px solid #e5e7ec;border-radius:3px;padding:6px}
.ec-card-sizes .ec-size-disabled{text-decoration:line-through;opacity:.42}
.ec-view-product{border:1px solid #1b2738;background:#fff;color:#1b2738;width:100%;display:flex;align-items:center;justify-content:center;gap:12px;font-weight:900;font-size:11px;text-transform:uppercase;letter-spacing:.7px;padding:12px;border-radius:3px;transition:background .15s,color .15s}
.ec-view-product:hover{background:#1b2738;color:#fff}
.ec-pagination-bar{padding-top:30px;display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:15px;color:#7b8390;font-size:11px}
.ec-pagination-bar .pagination{margin:0}.ec-pagination-bar .page-link{border-radius:3px!important;font-size:12px}
.ec-no-results{grid-column:1/-1;text-align:center;padding:65px 18px;border:1px dashed #dce2ec;background:#f9fafb;border-radius:6px}
.ec-no-results i{font-size:34px;color:#bdc7d5}.ec-no-results h3{font-size:18px;margin:15px 0 6px;font-weight:900}
.ec-no-results p{font-size:12px;color:#7c8898}.ec-no-results a{display:inline-block;padding:12px 17px;background:var(--ec-dark);color:#fff!important;font-weight:750;font-size:12px;border-radius:4px}
.ec-store-footer{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px;padding:28px clamp(20px,3vw,54px);background:#151b26;color:#c3ccd8;font-size:11px}
.ec-store-footer>div{display:flex;align-items:center;gap:9px}
.ec-footer-logo{display:flex;align-items:center;justify-content:center;background:#e53b48;color:#fff;width:38px;height:34px;border-radius:4px;font-size:20px;font-weight:950}
.ec-store-footer strong{font-size:14px;color:#fff}.ec-store-footer small{font-size:10px;margin-left:7px;color:#93a1b5}.ec-store-footer>a{color:#fff!important;font-weight:750}
/* Vista rápida de detalle; fuera del contenedor principal para evitar recortes del AdminLTE */
.ec-product-dialog{border:0!important;border-radius:8px;padding:0;max-width:min(960px,calc(100vw - 30px));width:960px;max-height:calc(100vh - 32px);overflow:auto;box-shadow:0 24px 85px rgba(0,0,0,.32);color:#1d2939}
.ec-product-dialog::backdrop{background:rgba(13,20,30,.75)}
.ec-dialog-header{position:sticky;top:0;z-index:5;display:flex;align-items:center;justify-content:space-between;gap:20px;background:#fff;border-bottom:1px solid #e8ebf1;padding:14px 20px;font-size:11px;letter-spacing:1px;font-weight:850}
.ec-dialog-header i{color:#e53b48;margin-right:7px}
.ec-dialog-close{width:33px;height:33px;display:flex;align-items:center;justify-content:center;background:#f3f5f7;border:none;border-radius:4px;color:#202c3b}
.ec-dialog-close:hover{background:#eee2e4}
.ec-detail-grid{display:grid;grid-template-columns:45% 55%;gap:0}
.ec-detail-image{position:relative;min-height:510px;background:#f6f7f9;display:flex;align-items:center;justify-content:center}
.ec-detail-image>img{width:100%;height:100%;max-height:630px;object-fit:contain;padding:18px}
.ec-detail-content{padding:clamp(22px,3vw,35px);min-width:0}
.ec-detail-kicker{font-size:11px;font-weight:900;color:#d63745;letter-spacing:1.4px;text-transform:uppercase}
.ec-detail-content h2{font-size:clamp(23px,2vw,30px)!important;color:#192434!important;line-height:1.25;font-weight:950;margin:10px 0 8px!important}
.ec-detail-sku{font-size:12px;color:#8390a0}.ec-detail-sku strong{color:#303d4f}
.ec-detail-price{font-size:27px;font-weight:950;letter-spacing:-.5px;color:#131c2b;margin:19px 0}
.ec-detail-price small{display:block;font-size:13px;color:#8995a6;font-weight:650;margin-top:5px}
.ec-detail-rule{border-top:1px solid #e7e9ef;margin:18px 0}
.ec-detail-content h3{font-size:12px;font-weight:900;margin-bottom:14px}
.ec-detail-variants{display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:8px;max-height:165px;overflow:auto}
.ec-detail-variant{padding:9px;border:1px solid #e3e8ed;border-radius:4px;display:grid;grid-template-columns:auto 1fr;gap:5px}
.ec-detail-variant b{font-size:12px}.ec-detail-variant span{font-size:9px;color:#98a2b0;align-self:center;text-align:right}
.ec-detail-variant small{font-size:10px;color:#4e637c;grid-column:1/-1}
.ec-detail-total{display:flex;align-items:center;justify-content:space-between;gap:10px;background:#f4f6f9;padding:11px 13px;border-radius:4px;font-size:11px;margin-top:13px}
.ec-detail-total strong{font-size:13px;color:#1c2e43}
.ec-detail-metadata{display:flex;flex-wrap:wrap;gap:10px;font-size:10px;color:#8b97a7;margin-top:15px}
.ec-detail-metadata span{border:1px solid #e5e8ee;border-radius:4px;padding:6px 8px}
.ec-detail-metadata strong{color:#31425a}
.ec-detail-manage{border-top:1px solid #e7eaf0;margin-top:21px;padding-top:19px}
.ec-detail-manage label{display:block;font-size:11px;color:#758497;font-weight:700}
.ec-detail-inline{display:flex;gap:7px;margin-top:6px}
.ec-detail-inline select{border:1px solid #dce2eb;min-width:0;flex:1;background:#fff;border-radius:4px;min-height:40px;padding:8px;color:#344153}
.ec-detail-inline button{background:#202a3b;color:#fff;border:0;border-radius:4px;font-weight:800;padding:8px 15px}
.ec-detail-remove button{background:transparent;border:0;color:#ad5862;font-size:11px;cursor:pointer;padding:14px 0 0}
body.ec-fullscreen .main-sidebar, body.ec-fullscreen .main-header, body.ec-fullscreen .main-footer{display:none!important}
body.ec-fullscreen .content-wrapper{margin-left:0!important;min-height:100vh!important}
body.ec-fullscreen .ec-storefront{min-height:100vh}
body.ec-fullscreen .ec-shop-header,body.ec-fullscreen .ec-shop-body{max-width:2000px}
@media(min-width:1700px){.ec-products-grid{grid-template-columns:repeat(4,minmax(0,1fr))}}
@media(max-width:1320px){.ec-header-actions span{display:none}.ec-header-actions a,.ec-header-actions button{min-width:42px}.ec-products-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.ec-shop-columns{grid-template-columns:205px minmax(0,1fr);gap:19px}}
@media(max-width:970px){.ec-hero-aside{width:43%}.ec-shop-columns{grid-template-columns:1fr}.ec-filters{display:none}.ec-filters.ec-filters-open{display:block;grid-row:1}.ec-mobile-filter-btn{display:inline-flex}.ec-products-grid{grid-template-columns:repeat(3,minmax(0,1fr))}.ec-info-strip{flex-wrap:wrap}.ec-info-strip span{width:calc(50% - 10px)}}
@media(max-width:760px){.ec-service-note{display:none}.ec-shop-header{flex-wrap:wrap;min-height:auto;padding-top:15px;padding-bottom:15px}.ec-brand{flex:1}.ec-search-form{order:3;flex:0 0 100%;max-width:none}.ec-hero{margin-top:16px;min-height:210px}.ec-hero-aside{width:34%;opacity:.55}.ec-hero-copy{padding:29px}.ec-hero:after{inset:0}.ec-hero h1{font-size:30px!important}.ec-hero p{max-width:350px}.ec-products-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}.ec-detail-grid{grid-template-columns:1fr}.ec-detail-image{min-height:300px;max-height:380px}.ec-detail-image img{max-height:350px}.ec-category-bar{gap:7px}.ec-category-label{display:none}}
@media(max-width:515px){.ec-service-bar,.ec-shop-header,.ec-category-bar,.ec-info-strip{padding-left:13px;padding-right:13px}.ec-hero{margin-left:13px;margin-right:13px}.ec-shop-body{padding:17px 13px 35px}.ec-info-strip span{font-size:10px}.ec-products-grid{gap:10px}.ec-card-information{padding:11px}.ec-product-card h3{font-size:12px!important;min-height:34px}.ec-card-price strong{font-size:16px}.ec-card-sizes{gap:3px}.ec-card-sizes>span:not(:first-child){min-width:22px;padding:4px}.ec-view-product{font-size:10px;padding:10px 4px}.ec-listing-head h2{font-size:22px!important}.ec-hero-aside{display:none}.ec-hero-copy{padding:24px}.ec-hero h1{font-size:28px!important}.ec-store-footer{padding:24px 16px}.ec-store-footer>div{width:100%}.ec-store-footer small{display:none}.ec-quick-icon{width:29px;height:29px}}
@media(prefers-reduced-motion:reduce){.ec-storefront *{transition:none!important;scroll-behavior:auto!important}}
</style>