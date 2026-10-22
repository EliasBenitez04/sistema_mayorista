<style>
/* Diseño de formularios de alta; limitado a .ref-create para no afectar otros módulos */
.ref-create{max-width:1180px;margin:0 auto 32px}
.ref-create .ref-create-heading{display:flex;align-items:center;gap:14px;margin:12px 0 20px}
.ref-create .ref-create-icon{width:50px;height:50px;display:grid;place-items:center;background:#edf2ff;color:#3659a2;border-radius:14px;font-size:21px;flex-shrink:0}
.ref-create .ref-create-heading h1{font-size:24px;font-weight:750;color:#263449;margin:0}
.ref-create .ref-create-heading p{color:#778396;margin:4px 0 0;font-size:13px}
.ref-create .ref-create-card{border:1px solid #e8edf3;border-radius:16px;box-shadow:0 8px 30px rgba(32,49,75,.065);overflow:hidden;background:#fff}
.ref-create .ref-create-card-header{background:linear-gradient(110deg,#f9fbff,#f2f6fc);border-bottom:1px solid #e7ecf4;padding:18px 24px}
.ref-create .ref-create-card-header h2{font-weight:700;font-size:16px;color:#293c56;margin:0}
.ref-create .ref-create-card-header small{display:block;color:#7c8b9e;margin-top:4px;font-size:12px}
.ref-create .ref-create-card .card-body{padding:28px}
.ref-create .ref-create-card .card-body .row{row-gap:4px}
.ref-create .ref-create-card .form-group label,.ref-create .ref-create-card label.form-label{font-size:13px;color:#344054;font-weight:600;margin-bottom:7px}
.ref-create .ref-create-card .form-control{min-height:41px;border:1px solid #dce4ed;border-radius:8px;background:#fff;box-shadow:none;transition:border-color .2s,box-shadow .2s}
.ref-create .ref-create-card textarea.form-control{min-height:95px}
.ref-create .ref-create-card .form-control:focus{border-color:#6d96db;box-shadow:0 0 0 3px rgba(75,125,206,.12)}
.ref-create .ref-create-card .input-group-text{min-height:41px;background:#f5f8fc;border:1px solid #dce4ed;color:#64748b}
.ref-create .ref-create-card .form-group{margin-bottom:19px}
.ref-create .ref-create-card .card-footer{display:flex;justify-content:flex-end;align-items:center;gap:10px;flex-wrap:wrap;background:#f9fafc;border-top:1px solid #e9eef4;padding:18px 24px}
.ref-create .ref-create-card .card-footer .btn{border-radius:8px;padding:10px 22px;font-size:13px;font-weight:650;min-width:118px}
.ref-create .ref-create-card .card-footer .btn-success{background:#228855;border-color:#228855}
.ref-create .ref-create-card .card-footer .btn-primary,.ref-create .ref-create-card .card-footer .btn-default{background:#fff;border:1px solid #d5deea;color:#45556b}
.ref-create .ref-create-card .card-footer .btn:hover{filter:brightness(.97)}
.ref-create .ref-create-card .table-responsive{border-radius:10px}
@media(max-width:767px){.ref-create{margin-bottom:16px}.ref-create .ref-create-heading h1{font-size:20px}.ref-create .ref-create-card .card-body{padding:18px}.ref-create .ref-create-card .card-footer{padding:16px;justify-content:stretch}.ref-create .ref-create-card .card-footer .btn{flex:1}.ref-create .ref-create-card-header{padding:16px}}
</style>
