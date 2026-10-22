<style>
/* Estilo unificado para tablas de mantenimiento y referenciales */
.ref-table-card { border:0; border-radius:12px; overflow:hidden; box-shadow:0 3px 12px rgba(0,0,0,.08); }
.ref-table-card .table { margin-bottom:0; border:0; font-size:13px; box-shadow:none; border-radius:0; }
.ref-table-card .table thead th { background:#343a40; color:#fff; border:0; padding:13px 10px; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.3px; white-space:nowrap; vertical-align:middle; }
.ref-table-card .table tbody td { vertical-align:middle; padding:11px 10px; border-top:1px solid #edf0f2; color:#495057; }
.ref-table-card .table tbody tr { transition:background-color .18s ease; }
.ref-table-card .table tbody tr:hover { background:#f8f9fc; }
.ref-table-card .ref-id { display:inline-flex; align-items:center; justify-content:center; min-width:30px; height:30px; padding:0 8px; border-radius:7px; background:#f1f3f5; color:#495057; font-size:12px; font-weight:700; }
.ref-table-card .ref-primary { font-weight:600; color:#343a40; line-height:1.3; }
.ref-table-card .ref-secondary { color:#6c757d; }
.ref-table-card .ref-actions { display:flex; align-items:center; justify-content:center; gap:6px; }
.ref-table-card .ref-actions .btn { width:34px; height:34px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:7px; transition:all .2s ease; margin:0; }
.ref-table-card .ref-actions .btn:hover { transform:translateY(-2px); box-shadow:0 3px 7px rgba(0,0,0,.15); }
.ref-table-card .ref-actions .btn i { font-size:13px; }
.ref-table-card .ref-footer { padding:14px 18px; background:#f8f9fa; border-top:1px solid #e9ecef; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
.ref-table-card .ref-footer-info { font-size:12px; color:#6c757d; }
.ref-table-card .ref-footer-info strong { color:#343a40; margin:0 3px; }
.ref-table-card .ref-footer .pagination { margin-bottom:0; }
@media(max-width:768px) { .ref-table-card .table { font-size:12px; } .ref-table-card .table th,.ref-table-card .table td { padding:9px 8px; } .ref-table-card .ref-footer { justify-content:center; text-align:center; } }
</style>