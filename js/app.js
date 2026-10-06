// app.js — Quintas de San Sebastián v2 (sin dependencias salvo Leaflet).
// Pantallas: #/ listado · #/quinta/ID detalle · #/reservar/ID formulario · #/reservas mis reservas
"use strict";

// ---------- Datos de DEMOSTRACIÓN (reemplazar por quintas reales autorizadas) ----------
const BASE = [["Chicharrón cusqueño", "Con mote, papa y ensalada", 38], ["Adobo de la casa", "Cerdo, chicha de jora y pan", 32], ["Cuy al horno", "Con papas doradas y tallarín", 55]];
const CC = "Cusqueña y regional", PA = "Parrillas", CA = "Comida casera";
const q = (id, n, z, cat, esp, a, b, srv, h, cap, lat, lng) => ({ id, n, z, cat, esp, a, b, srv, h, cap, lat, lng });
const Q = [
  q(1, "Quinta Los Jardines", "Zona central", CC, "Comida cusqueña", 35, 55, ["Jardín", "Salón interior", "Estacionamiento", "Acceso sin escalones"], ["13:00", "13:30", "14:00"], 38, -13.5333, -71.9167),
  q(2, "Quinta El Fogón Sebastiano", "Zona sur", PA, "Parrillas y comida regional", 40, 65, ["Terraza", "Juegos infantiles"], ["12:30", "13:00", "14:00"], 30, -13.5385, -71.9230),
  q(3, "Quinta La Campiña", "Zona este", CC, "Comida tradicional", 30, 50, ["Jardín", "Juegos infantiles"], ["13:00", "14:00", "14:30"], 24, -13.5420, -71.9100),
  q(4, "Quinta Sabor de Casa", "Zona central", CA, "Comida casera y regional", 25, 45, ["Salón interior", "Estacionamiento"], ["12:00", "13:00", "13:30"], 40, -13.5300, -71.9200),
  q(5, "Quinta El Molle", "Plaza de San Sebastián", CC, "Chicharrón y lechón", 30, 48, ["Jardín", "Estacionamiento"], ["12:00", "13:00", "14:00"], 36, -13.5355, -71.9150),
  q(6, "Quinta Tumbo", "Larapa", PA, "Cuy y parrillas", 45, 70, ["Terraza", "Acceso sin escalones"], ["13:00", "13:30", "14:30"], 28, -13.5400, -71.9260),
  q(7, "Quinta Los Retamas", "Larapa", CA, "Trucha y comida casera", 28, 42, ["Jardín", "Juegos infantiles"], ["12:30", "13:30", "14:00"], 24, -13.5440, -71.9120),
  q(8, "Quinta Sol de Maíz", "Plaza de San Sebastián", CC, "Chicharrón cusqueño", 32, 52, ["Jardín", "Salón interior", "Estacionamiento"], ["12:00", "12:30", "13:00"], 50, -13.5290, -71.9210),
];
const FIL = { cat: [CC, PA, CA], pre: ["Hasta S/ 30", "S/ 30 a S/ 50", "Más de S/ 50"], srv: ["Jardín o terraza", "Estacionamiento", "Juegos infantiles", "Acceso sin escalones"] };
const HORAS = ["12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00"], PERS = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

// ---------- Utilidades ----------
const $ = (s, r = document) => r.querySelector(s), $$ = (s, r = document) => [...r.querySelectorAll(s)];
const app = $("#app");
const esc = s => String(s).replace(/[&<>"']/g, c => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c])); // evita XSS
const hoy = () => new Date(Date.now() - new Date().getTimezoneOffset() * 6e4).toISOString().slice(0, 10); // fecha local
const fLarga = f => new Date(f + "T12:00").toLocaleDateString("es-PE", { weekday: "long", day: "numeric", month: "long", year: "numeric" });
const fCorta = f => new Date(f + "T12:00").toLocaleDateString("es-PE", { weekday: "short", day: "numeric", month: "short" });
const opt = (arr, v, f = x => x) => arr.map(o => `<option value="${o}"${String(o) === String(v) ? " selected" : ""}>${f(o)}</option>`).join("");
const pl = n => n + " persona" + (n === 1 ? "" : "s");

// ---------- CRUD local de reservas ----------
const KEY = "quintas_ss_reservas";
const leer = () => { try { return JSON.parse(localStorage.getItem(KEY)) || []; } catch { return []; } };
const guardar = l => { try { localStorage.setItem(KEY, JSON.stringify(l)); } catch { /* sin almacenamiento */ } };
const ocup = (id, f, h) => leer().filter(r => r.quinta === id && r.fecha === f && r.hora === h).reduce((s, r) => s + r.pers, 0);
const libres = (x, f, p) => x.h.filter(h => ocup(x.id, f, h) + p <= x.cap); // horarios con cupo (control de aforo)

// ---------- Estado de la búsqueda ----------
const S = { q: "", fecha: hoy(), hora: "13:00", pers: 4, cat: [], pre: [], srv: [], orden: "rec", pag: 1 };
let manej = null; // manejador de eventos de la vista activa
["input", "change", "click"].forEach(ev => app.addEventListener(ev, e => manej && manej(e)));

// ---------- Menú móvil ----------
const cab = $(".site-header"), tog = $("#nav-toggle");
function menu(abrir) { cab.classList.toggle("nav-open", abrir); tog.setAttribute("aria-expanded", String(abrir)); tog.setAttribute("aria-label", abrir ? "Cerrar menú" : "Abrir menú"); }
tog.addEventListener("click", () => menu(!cab.classList.contains("nav-open")));
document.addEventListener("keydown", e => { if (e.key === "Escape" && cab.classList.contains("nav-open")) { menu(false); tog.focus(); } });
matchMedia("(min-width: 640px)").addEventListener("change", e => e.matches && menu(false));

// ================= VISTA 1: LISTADO =================
const grupo = (g, t) => `<fieldset class="gr"><legend>${t}</legend>${FIL[g].map(v => `<label class="chk"><input type="checkbox" data-g="${g}" value="${v}"${S[g].includes(v) ? " checked" : ""}> ${v}</label>`).join("")}</fieldset>`;
const tier = x => (x.a <= 30 ? 0 : x.a <= 50 ? 1 : 2);
const card = x => {
  const l = libres(x, S.fecha, S.pers);
  return `<li class="card q"><div class="foto" role="img" aria-label="Fotografía de la quinta (pendiente)">Fotografía de la quinta</div><div class="cuerpo">
    <h3>${x.n}</h3><p class="meta">${x.z} · San Sebastián</p>
    <p class="linea"><span>${x.esp}</span><span class="precio">S/ ${x.a}–${x.b} / persona</span></p>
    <p class="meta">${x.srv.join(" · ")}</p>
    <p class="meta">Para ${pl(S.pers)}: ${l.length ? l.join(" · ") : "sin cupo con esos datos"}</p>
    <a class="btn btn-borde btn-chico" href="#/quinta/${x.id}">Ver quinta →</a></div></li>`;
};
function pintarLista() {
  const t = S.q.trim().toLowerCase();
  const d = Q.filter(x => (!t || (x.n + " " + x.z).toLowerCase().includes(t)) && (!S.cat.length || S.cat.includes(x.cat)) &&
    (!S.pre.length || S.pre.includes(FIL.pre[tier(x)])) &&
    S.srv.every(s => (s === "Jardín o terraza" ? x.srv.some(y => y === "Jardín" || y === "Terraza") : x.srv.includes(s))));
  if (S.orden === "pa") d.sort((a, b) => a.a - b.a); else if (S.orden === "pd") d.sort((a, b) => b.a - a.a);
  const N = 4, pgs = Math.max(1, Math.ceil(d.length / N)); S.pag = Math.min(S.pag, pgs);
  $("#cnt").textContent = d.length + (d.length === 1 ? " quinta encontrada" : " quintas encontradas");
  $("#sub").textContent = `${fCorta(S.fecha)} · ${S.hora} · ${pl(S.pers)}`;
  $("#lista").innerHTML = d.slice((S.pag - 1) * N, S.pag * N).map(card).join("") || `<li class="vacio">No hay quintas con esos filtros. Prueba quitar alguno.</li>`;
  $("#pag").innerHTML = pgs < 2 ? "" : Array.from({ length: pgs }, (_, i) => `<button type="button" class="btn btn-chico${S.pag === i + 1 ? "" : " btn-borde"}" data-pg="${i + 1}"${S.pag === i + 1 ? ' aria-current="page"' : ""}>${i + 1}</button>`).join("");
}
function vistaLista() {
  document.title = "Explorar quintas | Quintas de San Sebastián";
  app.innerHTML = `<section class="hero" aria-labelledby="h1"><p class="meta">San Sebastián, Cusco</p>
    <h1 id="h1">Encuentra una quinta para compartir</h1><p>Compara la cocina, los espacios y el precio por persona. Elige tu quinta y reserva una mesa.</p>
    <div class="barra" role="search">
      <div class="campo"><label for="b-q">Nombre o zona</label><input id="b-q" placeholder="Todas las quintas" value="${esc(S.q)}"></div>
      <div class="campo"><label for="b-f">Fecha</label><input id="b-f" type="date" min="${hoy()}" value="${S.fecha}"></div>
      <div class="campo"><label for="b-h">Hora</label><select id="b-h">${opt(HORAS, S.hora)}</select></div>
      <div class="campo"><label for="b-p">Personas</label><select id="b-p">${opt(PERS, S.pers, pl)}</select></div>
      <button class="btn" id="b-ok" type="button">Buscar mesas</button></div></section>
    <div class="listado"><details class="filtros-p" id="fp"${matchMedia("(min-width: 900px)").matches ? " open" : ""}><summary>Filtros</summary>
      ${grupo("cat", "Tipo de cocina")}${grupo("pre", "Precio por persona")}${grupo("srv", "Servicios y espacios")}
      <button class="btn btn-borde btn-chico" id="limpiar" type="button">Limpiar filtros</button>
      <p class="nota">Los precios son referenciales y no incluyen el consumo de todo el grupo.</p></details>
      <div><div class="cab-res"><div><h2 id="cnt" aria-live="polite"></h2><p class="nota" id="sub"></p></div>
        <label>Ordenar <select id="ord"><option value="rec">Recomendadas</option><option value="pa">Precio: menor a mayor</option><option value="pd">Precio: mayor a menor</option></select></label></div>
      <ul id="lista" class="cards dos" style="margin-top:1rem"></ul><nav class="pag" id="pag" aria-label="Paginación"></nav></div></div>`;
  $("#ord").value = S.orden; pintarLista();
  manej = e => {
    const t = e.target;
    if (e.type === "click") {
      if (t.id === "limpiar") { S.cat = []; S.pre = []; S.srv = []; S.pag = 1; vistaLista(); }
      else if (t.dataset.pg) { S.pag = +t.dataset.pg; pintarLista(); $("#cnt").scrollIntoView({ block: "start" }); }
      else if (t.id === "b-ok") $("#cnt").scrollIntoView({ block: "start" });
      return;
    }
    if (t.id === "b-q") S.q = t.value; else if (t.id === "b-f" && t.value) S.fecha = t.value; else if (t.id === "b-h") S.hora = t.value;
    else if (t.id === "b-p") S.pers = +t.value; else if (t.id === "ord") S.orden = t.value;
    else if (t.dataset.g) S[t.dataset.g] = $$(`[data-g="${t.dataset.g}"]:checked`).map(i => i.value); else return;
    S.pag = 1; pintarLista();
  };
}

// ================= VISTA 2: DETALLE =================
function vistaQuinta(id) {
  const x = Q.find(o => o.id === +id); if (!x) return vistaVacia();
  document.title = x.n + " | Quintas de San Sebastián";
  S.pers = Math.min(12, Math.max(2, S.pers));
  app.innerHTML = `<nav class="migas" aria-label="Ruta"><a href="#/">Explorar quintas</a> / ${x.n}</nav>
    <div class="cab-res"><div><h1 class="t1">${x.n}</h1><p class="meta">${x.esp} · ${x.z} · San Sebastián · Establecimiento de ejemplo</p></div><a class="btn btn-borde" href="#/">← Volver al listado</a></div>
    <div class="detalle"><div>
      <div class="galeria" role="img" aria-label="Galería de fotografías (pendiente)"><div class="foto">Fotografía principal · Jardín y mesas</div><div class="foto">Fotografía · Salón</div><div class="foto">Fotografía · Platos de la casa</div></div>
      <section><h2>Un almuerzo en familia, al aire libre</h2><p>${x.esp} en ${x.z}. Una opción para almuerzos familiares y reuniones de grupos pequeños en San Sebastián.</p>
        <div class="datos"><span>Precio por persona<b>S/ ${x.a}–${x.b}</b></span><span>Horario referencial<b>Mar–dom · 12:00–17:00</b></span><span>Reservas en línea<b>De 2 a 12 personas</b></span></div></section>
      <section><h2>Espacios y servicios</h2><p>${x.srv.map(s => `<span class="tag">${s}</span>`).join("")}</p></section>
      <section><h2>Una muestra de la carta</h2>${BASE.map(p => `<div class="plato"><span><b>${p[0]}</b><br><span class="meta">${p[1]}</span></span><span>S/ ${p[2]}</span></div>`).join("")}
        <p class="nota">Platos y precios ilustrativos. El consumo se paga en el establecimiento.</p></section>
      <section><h2>Ubicación</h2><p>${x.z}, distrito de San Sebastián, Cusco. Confirma la dirección antes de visitar.</p>
        <div id="mapa-q" class="mapa-q" role="img" aria-label="Mapa de ubicación referencial"></div><p class="nota">En el celular, mueve el mapa con dos dedos.</p></section>
    </div><aside class="panel fijo" id="panel" aria-label="Reserva tu mesa"></aside></div>`;
  const panel = () => {
    const act = document.activeElement.id, lib = libres(x, S.fecha, S.pers);
    if (!lib.includes(S.hora)) S.hora = lib[0] || "";
    $("#panel").innerHTML = `<h2>Reserva tu mesa</h2><p class="nota">Elige la fecha y el tamaño de tu grupo.</p>
      <div class="campo"><label for="p-f">Fecha</label><input id="p-f" type="date" min="${hoy()}" value="${S.fecha}"></div>
      <div class="campo"><label for="p-p">Número de personas</label><select id="p-p">${opt(PERS, S.pers, pl)}</select></div>
      <p class="lbl" id="lh">Horarios disponibles</p><div class="horas" role="group" aria-labelledby="lh">${x.h.map(h => `<button type="button" id="h${h.replace(":", "")}" class="hora${h === S.hora ? " on" : ""}" data-h="${h}" aria-pressed="${h === S.hora}"${lib.includes(h) ? "" : " disabled"}>${h}</button>`).join("")}</div>
      <p class="nota">${S.hora ? S.hora + " seleccionado · Disponibilidad ilustrativa" : "Sin cupo para ese grupo. Prueba otra fecha o menos personas."}</p>
      <p><b>${fCorta(S.fecha)} · ${S.hora || "—"} · ${pl(S.pers)}</b></p>
      <a class="btn" href="#/reservar/${x.id}"${S.hora ? "" : ' aria-disabled="true" tabindex="-1"'}>Continuar con la reserva →</a>
      <p class="nota">Sin pago anticipado en este ejemplo.</p><h3>Antes de reservar</h3>
      <ul><li>Tolerancia de llegada: 15 minutos.</li><li>Puedes cancelar hasta 2 horas antes.</li><li>Condiciones de ejemplo, no verificadas.</li></ul>`;
    if (act) document.getElementById(act)?.focus();
  };
  panel();
  manej = e => {
    const t = e.target;
    if (e.type === "click") { if (t.dataset.h) { S.hora = t.dataset.h; panel(); } return; }
    if (t.id === "p-f" && t.value) S.fecha = t.value; else if (t.id === "p-p") S.pers = +t.value; else return;
    panel();
  };
  if (window.L) { // mapa Leaflet: en táctil se mueve con dos dedos para no atrapar el scroll
    const m = L.map("mapa-q", { scrollWheelZoom: false, dragging: !L.Browser.mobile }).setView([x.lat, x.lng], 15);
    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", { maxZoom: 19, attribution: "© colaboradores de OpenStreetMap" }).addTo(m);
    L.circleMarker([x.lat, x.lng], { radius: 11, color: "#fff", weight: 2, fillColor: "#E8590C", fillOpacity: .95 }).addTo(m);
  }
}

// ================= VISTA 3: RESERVA (3 pasos) =================
const pasos = n => `<ol class="pasos" aria-label="Pasos de la reserva"><li${n === 1 ? ' class="on"' : ""}>${n > 1 ? "✓ " : ""}Quinta y horario</li><li${n === 2 ? ' class="on" aria-current="step"' : ""}>${n > 2 ? "✓ " : "2 "}Datos y revisión</li><li${n === 3 ? ' class="on" aria-current="step"' : ""}>3 Confirmación</li></ol>`;
const C = (c, l, tag, at = "", inn = "") => `<div class="campo"><label for="${c}">${l}</label>${tag === "input" ? `<input id="${c}" ${at} aria-describedby="e-${c}">` : `<${tag} id="${c}" ${at} aria-describedby="e-${c}">${inn}</${tag}>`}<span class="error" id="e-${c}" role="alert"></span></div>`;
const err = (c, m) => ($("#e-" + c).textContent = m);
function vistaReserva(id) {
  const x = Q.find(o => o.id === +id); if (!x) return vistaVacia();
  document.title = "Reservar en " + x.n;
  S.pers = Math.min(12, Math.max(2, S.pers)); if (!x.h.includes(S.hora)) S.hora = libres(x, S.fecha, S.pers)[0] || x.h[0];
  app.innerHTML = `<nav class="migas" aria-label="Ruta"><a href="#/">Explorar quintas</a> / <a href="#/quinta/${x.id}">${x.n}</a> / Reservar mesa</nav>
    <h1 class="t1">Completa tu reserva</h1><p class="meta">Revisa tu mesa y déjanos tus datos de contacto. Solo tomará un momento.</p>${pasos(2)}
    <div class="res"><form id="form" novalidate><h2>1. Tu mesa</h2>
      <div class="fila">${C("fecha", "Fecha *", "input", `type="date" min="${hoy()}" value="${S.fecha}" required`)}${C("hora", "Hora *", "select", "required", opt(x.h, S.hora))}${C("pers", "Personas *", "select", "required", opt(PERS, S.pers, pl))}</div>
      <h2>2. Datos de contacto</h2><p class="nota">Usaremos estos datos para enviarte la confirmación. * Campos obligatorios.</p>
      <div class="fila">${C("nombres", "Nombres *", "input", 'autocomplete="given-name" required')}${C("apellidos", "Apellidos *", "input", 'autocomplete="family-name" required')}</div>
      <div class="fila">${C("correo", "Correo electrónico *", "input", 'type="email" autocomplete="email" required')}${C("tel", "Celular *", "input", 'type="tel" inputmode="numeric" autocomplete="tel-national" placeholder="900 000 000" maxlength="16" required')}</div>
      ${C("notas", "¿Algo que debamos saber? (opcional)", "textarea", 'maxlength="300"')}
      <h2>3. Revisa y confirma</h2><p class="nota">El consumo se paga directamente en la quinta. No se solicita tarjeta.</p>
      <div class="campo"><label class="chk"><input type="checkbox" id="ok" aria-describedby="e-ok"> Acepto las condiciones de reserva y el aviso de privacidad. *</label><span class="error" id="e-ok" role="alert"></span></div>
      <button class="btn" type="submit">Confirmar reserva →</button> <a class="btn btn-borde" href="#/quinta/${x.id}">← Volver a la quinta</a></form>
      <aside class="panel fijo" aria-label="Resumen de tu reserva"><h2>Resumen de tu reserva</h2><div class="foto" style="border-radius:var(--radio)">Fotografía de la quinta</div>
        <h3 style="margin-top:.8rem">${x.n}</h3><p class="meta">${x.esp} · ${x.z}, Cusco</p><div id="resumen"></div>
        <p class="meta">Consumo referencial: S/ ${x.a}–${x.b} por persona. No es un cobro ni una cotización.</p><p><b>A pagar ahora: S/ 0</b></p>
        <p class="nota">Tolerancia 15 minutos · Cancelación hasta 2 horas antes · Condiciones ilustrativas.</p></aside></div>`;
  const resumen = () => ($("#resumen").innerHTML = `<p><span class="meta">Fecha</span><br><b>${esc($("#fecha").value ? fLarga($("#fecha").value) : "—")}</b></p><p><span class="meta">Hora y grupo</span><br><b>${esc($("#hora").value)} h · ${pl(+$("#pers").value)}</b></p>`);
  resumen();
  manej = e => { if (e.type !== "click" && ["fecha", "hora", "pers"].includes(e.target.id)) resumen(); };
  $("#form").addEventListener("submit", ev => {
    ev.preventDefault(); let ok = true;
    ["fecha", "hora", "pers", "nombres", "apellidos", "correo", "tel", "ok"].forEach(c => err(c, ""));
    const bad = (c, m) => { err(c, m); ok = false; }, v = k => $("#" + k).value.trim(), n = +v("pers");
    if (!v("fecha") || v("fecha") < hoy()) bad("fecha", "Elige una fecha de hoy en adelante.");
    if (!v("hora")) bad("hora", "Elige una hora.");
    if (!(n >= 2 && n <= 12)) bad("pers", "Indica entre 2 y 12 personas.");
    if (v("nombres").length < 2) bad("nombres", "Escribe tus nombres (mínimo 2 letras).");
    if (v("apellidos").length < 2) bad("apellidos", "Escribe tus apellidos (mínimo 2 letras).");
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v("correo"))) bad("correo", "Escribe un correo válido, por ejemplo nombre@correo.com.");
    const tel = v("tel").replace(/[\s+-]/g, "").replace(/^51(?=9\d{8}$)/, "");
    if (!/^9\d{8}$/.test(tel)) bad("tel", "Ingresa un celular de 9 dígitos que empiece con 9.");
    if (!$("#ok").checked) bad("ok", "Debes aceptar las condiciones para continuar.");
    if (ok && ocup(x.id, v("fecha"), v("hora")) + n > x.cap) bad("pers", "No hay cupo para ese horario. Prueba otra hora o menos personas.");
    if (!ok) { const f = $$(".error", ev.target).find(s => s.textContent); if (f) $("#" + f.id.slice(2)).focus(); return; }
    const l = leer(), r = { id: Date.now(), codigo: "RES-" + Date.now().toString(36).slice(-5).toUpperCase(), quinta: x.id, fecha: v("fecha"), hora: v("hora"), pers: n, nombre: v("nombres") + " " + v("apellidos"), correo: v("correo"), tel, notas: v("notas") };
    l.push(r); guardar(l); S.fecha = r.fecha; S.hora = r.hora; S.pers = n; manej = null;
    app.innerHTML = `${pasos(3)}<section class="panel" style="margin-top:1rem"><h1 class="t1">Reserva confirmada</h1><p class="ok">Código: ${r.codigo}</p>
      <p>${esc(x.n)} · ${esc(fLarga(r.fecha))} · ${r.hora} h · ${pl(r.pers)}</p><p class="meta">Enviaremos el detalle a ${esc(r.correo)}. (Ejemplo: no se envía ningún correo real.)</p>
      <a class="btn" href="#/reservas">Ver mis reservas</a><a class="btn btn-borde" href="#/">Seguir explorando</a></section>`;
    app.focus(); scrollTo(0, 0);
  });
}

// ================= VISTA 4: MIS RESERVAS =================
function vistaMis() {
  document.title = "Mis reservas | Quintas de San Sebastián";
  const pintar = () => {
    const l = leer();
    $("#reservas").innerHTML = l.map(r => { const x = Q.find(o => o.id === r.quinta);
      return `<li class="card"><h3>${r.codigo} · ${x ? x.n : "Quinta"}</h3><p>${esc(fCorta(r.fecha))} a las ${r.hora} · ${pl(r.pers)}</p><p class="meta">A nombre de ${esc(r.nombre)}</p>
      <button type="button" class="btn btn-chico btn-borde" data-del="${r.id}">Cancelar reserva</button></li>`; }).join("") || `<li class="vacio">Todavía no tienes reservas. Elige una quinta y reserva tu mesa.</li>`;
  };
  app.innerHTML = `<section><h1 class="t1">Mis reservas</h1><ul id="reservas" class="cards" aria-live="polite"></ul></section>`;
  pintar();
  manej = e => { const d = e.type === "click" && e.target.dataset.del; if (d) { guardar(leer().filter(r => String(r.id) !== d)); pintar(); } };
}
function vistaVacia() { document.title = "No encontrada"; manej = null; app.innerHTML = `<section><h1 class="t1">No encontramos esa quinta</h1><p><a class="btn" href="#/">Ver todas las quintas</a></p></section>`; }

// ---------- Enrutador por hash ----------
let primera = true;
function ruta() {
  const [, a, b] = location.hash.slice(1).split("/"); manej = null;
  (a === "quinta" ? vistaQuinta(b) : a === "reservar" ? vistaReserva(b) : a === "reservas" ? vistaMis() : vistaLista());
  scrollTo(0, 0); menu(false); if (!primera) app.focus({ preventScroll: true }); primera = false;
}
addEventListener("hashchange", ruta); ruta();
