:root {
  --brand-1: #4f46e5;
  --brand-2: #4338ca;
  --brand-soft: #eef2ff;
}

body {
  background-color: #f4f6fb;
  font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
}

.app-navbar {
  background: linear-gradient(90deg, var(--brand-1), var(--brand-2));
}

/* ---------- Stat cards ---------- */
.stat-card {
  border: none;
  border-radius: 14px;
  color: #fff;
  padding: 1.25rem 1.5rem;
  box-shadow: 0 6px 18px rgba(79, 70, 229, 0.18);
  height: 100%;
}
.stat-card .stat-icon {
  font-size: 1.8rem;
  opacity: 0.85;
}
.stat-card .stat-value {
  font-size: 1.9rem;
  font-weight: 700;
  line-height: 1.1;
}
.stat-card .stat-label {
  font-size: 0.85rem;
  opacity: 0.9;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.stat-card.bg-total   { background: linear-gradient(135deg, #4f46e5, #6366f1); }
.stat-card.bg-avg     { background: linear-gradient(135deg, #059669, #10b981); }
.stat-card.bg-active  { background: linear-gradient(135deg, #0891b2, #06b6d4); }
.stat-card.bg-branch  { background: linear-gradient(135deg, #d97706, #f59e0b); }

/* ---------- Cards / panels ---------- */
.panel-card {
  border: none;
  border-radius: 16px;
  box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
}

/* ---------- Table ---------- */
.table thead th {
  background-color: var(--brand-soft);
  color: var(--brand-2);
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  border-bottom: none;
  white-space: nowrap;
}
.table > :not(caption) > * > * {
  vertical-align: middle;
}
.student-photo {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--brand-soft);
}
.avatar-fallback {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: var(--brand-soft);
  color: var(--brand-1);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
}

.branch-badge {
  background-color: var(--brand-soft);
  color: var(--brand-2);
  font-weight: 500;
}

/* ---------- Forms ---------- */
.form-card {
  max-width: 720px;
  margin: 0 auto;
  border-radius: 16px;
}
.photo-preview {
  width: 90px;
  height: 90px;
  border-radius: 12px;
  object-fit: cover;
  border: 1px solid #dee2e6;
}

@media (max-width: 576px) {
  .stat-card .stat-value { font-size: 1.5rem; }
}
