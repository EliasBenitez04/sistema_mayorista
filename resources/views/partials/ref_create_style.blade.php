<style>
/* Formularios de alta v2: identidad limpia, lectura clara y zonas de acción */
.ref-create{--ink:#243247;--muted:#64748b;--line:#e6ebf1;--accent:#2563eb;max-width:1120px;margin:12px auto 48px;color:var(--ink)}
.ref-create.ref-create--simple{max-width:780px}
.ref-create .ref-create-heading{display:flex;align-items:center;gap:16px;margin:0 0 22px;padding:0 2px}
.ref-create .ref-create-icon{width:54px;height:54px;flex-shrink:0;border-radius:15px;background:#eaf1ff;color:#245ac2;display:flex;align-items:center;justify-content:center;font-size:22px;border:1px solid #d9e6ff}
.ref-create .ref-create-heading h1{font-size:25px;letter-spacing:-.5px;font-weight:750;line-height:1.2;color:#1f2c3f;margin:0}
.ref-create .ref-create-heading p{margin:6px 0 0;color:var(--muted);font-size:13px}
.ref-create .ref-create-card{border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;background:#fff;box-shadow:0 12px 32px rgba(31,45,66,.055)}
.ref-create .ref-create-card-header{background:#fff;border-bottom:1px solid var(--line);padding:24px 30px 22px;display:flex;gap:14px;align-items:flex-start}
.ref-create .ref-create-section-icon{display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:11px;background:#eff6ff;color:#2c63bf;flex-shrink:0;font-size:16px}
.ref-create .ref-create-card-header h2{font-weight:750;letter-spacing:-.2px;font-size:17px;color:#233247;margin:1px 0 5px}
.ref-create .ref-create-card-header small{display:block;color:#7a8799;font-size:12px;line-height:1.5}
.ref-create .ref-create-card .card-body{padding:28px 30px 18px;background:#fff}
.ref-create .ref-create-card .card-body>.row{row-gap:0;margin-left:-10px;margin-right:-10px}
.ref-create .ref-create-card .card-body>.row>[class*="col-"],.ref-create .ref-create-card .card-body>.row>.row>[class*="col-"]{padding-left:10px;padding-right:10px}
.ref-create .ref-create-card .form-group{margin-bottom:21px}
.ref-create .ref-create-card label:not(.custom-control-label){font-weight:650;color:#334155;font-size:13px;margin-bottom:8px;line-height:1.4}
.ref-create .ref-create-card .form-control{height:44px;border:1px solid #dbe3ed;border-radius:9px;color:#263449;background:#fff;box-shadow:none;font-size:14px;transition:border-color .16s ease,box-shadow .16s ease}
.ref-create .ref-create-card textarea.form-control{height:auto;min-height:94px;resize:vertical;padding-top:11px}
.ref-create .ref-create-card .form-control::placeholder{color:#a0aab8}
.ref-create .ref-create-card .form-control:hover:not(:disabled){border-color:#b7c5d5}
.ref-create .ref-create-card .form-control:focus{border-color:#5488e4;box-shadow:0 0 0 3px rgba(37,99,235,.1)}
.ref-create .ref-create-card .form-control:disabled,.ref-create .ref-create-card .form-control[readonly]{background:#f6f8fb;color:#64748b}
.ref-create .ref-create-card .input-group-text{height:44px;background:#f6f8fb;border:1px solid #dbe3ed;color:#708199;border-radius:9px}
.ref-create .ref-create-card .input-group-prepend .input-group-text{border-top-right-radius:0;border-bottom-right-radius:0}
.ref-create .ref-create-card .input-group-prepend+.form-control{border-top-left-radius:0;border-bottom-left-radius:0}
.ref-create .ref-create-card .form-text,.ref-create .ref-create-card small.text-muted{font-size:12px;color:#77859a}
.ref-create .ref-create-card .table-responsive{border-radius:10px;border:1px solid #e5eaf0}
.ref-create .ref-create-card table th{background:#f5f8fc;color:#40516a;font-weight:700;border-color:#e6ecf2}
.ref-create .ref-create-card table td{border-color:#edf0f4}
.ref-create .ref-create-card .card-footer{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;padding:18px 30px;background:#f8fafc;border-top:1px solid #e5eaf0}
.ref-create .ref-create-footer-note{font-size:12px;color:#758399;display:flex;align-items:center;gap:7px;margin-right:auto}
.ref-create .ref-create-footer-actions{display:flex;align-items:center;gap:10px;margin-left:auto}
.ref-create .ref-create-card .card-footer .btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;font-size:13px;font-weight:700;min-height:42px;min-width:125px;padding:9px 19px;border-radius:9px;box-shadow:none}
.ref-create .ref-create-card .card-footer .btn-success{background:#21845a;border-color:#21845a;color:#fff}
.ref-create .ref-create-card .card-footer .btn-success:hover{background:#196d49;border-color:#196d49}
.ref-create .ref-create-card .card-footer .btn-primary,.ref-create .ref-create-card .card-footer .btn-default{background:#fff;color:#475569;border:1px solid #d5dfe9}
.ref-create .ref-create-card .card-footer .btn-primary:hover,.ref-create .ref-create-card .card-footer .btn-default:hover{background:#f1f5f9}
.ref-create .ref-create-card input[type=checkbox]{accent-color:#2563eb}
@media(max-width:767px){.ref-create{margin:6px auto 24px}.ref-create .ref-create-heading{gap:12px;margin-bottom:16px}.ref-create .ref-create-icon{width:46px;height:46px;font-size:19px}.ref-create .ref-create-heading h1{font-size:21px}.ref-create .ref-create-heading p{font-size:12px}.ref-create .ref-create-card-header{padding:18px;gap:11px}.ref-create .ref-create-card .card-body{padding:20px 18px 8px}.ref-create .ref-create-card .card-footer{padding:15px 18px}.ref-create .ref-create-footer-note{flex-basis:100%}.ref-create .ref-create-footer-actions{width:100%;margin-left:0}.ref-create .ref-create-footer-actions .btn{flex:1;min-width:0}}

/* V3: formularios referenciales más compactos y visualmente equilibrados */
.ref-create{max-width:1040px;margin-top:20px}
.ref-create.ref-create--simple{max-width:570px}
.ref-create .ref-create-heading{margin-bottom:17px}
.ref-create .ref-create-heading h1{font-size:23px}
.ref-create .ref-create-card{border-radius:14px;box-shadow:0 14px 36px rgba(31,48,71,.085);position:relative}
.ref-create .ref-create-card:before{content:"";display:block;height:4px;background:linear-gradient(90deg,#2563eb,#38bdf8)}
.ref-create .ref-create-card-header{padding:19px 24px 18px;background:linear-gradient(180deg,#fff,#fcfdff)}
.ref-create .ref-create-card-header h2{font-size:15px}
.ref-create .ref-create-card .card-body{padding:25px 24px 13px}
.ref-create.ref-create--simple .ref-create-card .card-body>.row>.form-group,
.ref-create.ref-create--simple .ref-create-card .card-body>.row>[class*="col-"]{flex:0 0 100%;max-width:100%}
.ref-create.ref-create--simple .ref-create-card .card-body>.row>.row{width:100%;margin-left:0;margin-right:0}
.ref-create.ref-create--simple .ref-create-card .card-body>.row>.row>.form-group,
.ref-create.ref-create--simple .ref-create-card .card-body>.row>.row>[class*="col-"]{flex:0 0 100%;max-width:100%}
.ref-create.ref-create--simple .ref-create-card .form-control{width:100%}
.ref-create .ref-create-card .form-control{background-color:#fbfdff}
.ref-create .ref-create-card .form-control:focus{background-color:#fff}
.ref-create .ref-create-card .card-footer{padding:14px 24px;background:#f8fafc}
.ref-create .ref-create-card .card-footer .btn{min-height:41px}
.ref-create.ref-create--simple .ref-create-footer-note{display:none}
.ref-create.ref-create--simple .ref-create-footer-actions{width:100%;justify-content:flex-end}
.ref-create.ref-create--simple .ref-create-card .form-group{margin-bottom:16px}
.ref-create.ref-create--simple .ref-create-icon{background:#eaf1ff}
@media(max-width:767px){.ref-create{margin-top:8px}.ref-create .ref-create-card-header{padding:17px}.ref-create .ref-create-card .card-body{padding:20px 17px 9px}.ref-create .ref-create-card .card-footer{padding:14px 17px}}
</style>