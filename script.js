let canciones = [];
let cancionesFiltradas = [];
let playlists = [];
let indiceActual = 0;
let player;
let barra;
let btn;
let playlistActual = null;


function cerrarSesion() {
    if (confirm("¿Seguro que quieres cerrar sesión?")) {
        window.location.href = "logout.php";
    }
}

async function cargarCanciones() {
    try {
        let res = await fetch("obtener.php");
        canciones = await res.json();
        
        if (playlistActual) {
            await cargarCancionesDePlaylist(playlistActual);
        } else {
            cancionesFiltradas = canciones;
            mostrarCanciones(cancionesFiltradas);
        }
    } catch (error) {
        console.error("Error:", error);
    }
}

async function cargarPlaylists() {
    try {
        let res = await fetch("playlists.php");
        playlists = await res.json();
        mostrarPlaylists();
    } catch (error) {
        console.error("Error cargando playlists:", error);
    }
}

function mostrarPlaylists() {
    let header = document.querySelector("header");
    let playlistsContainer = document.getElementById("playlists-container");
    
    if (!playlistsContainer) {
        playlistsContainer = document.createElement("section");
        playlistsContainer.id = "playlists-container";
        header.appendChild(playlistsContainer);
    }
    
    let html = '<button onclick="verTodasLasCanciones()">Todas</button>';
    
    playlists.forEach(p => {
        html += `<button onclick="verPlaylist(${p.id})">${p.nombre}</button>`;
    });
    
    html += `<button onclick="mostrarDialogoCrearPlaylist()">Nueva Playlist</button>`;
    
    playlistsContainer.innerHTML = html;
}

function verTodasLasCanciones() {
    playlistActual = null;
    cargarCanciones();
}

async function verPlaylist(id) {
    playlistActual = id;
    await cargarCancionesDePlaylist(id);
}

async function cargarCancionesDePlaylist(idPlaylist) {
    try {
        let res = await fetch(`canciones_playlist.php?id=${idPlaylist}`);
        cancionesFiltradas = await res.json();
        mostrarCanciones(cancionesFiltradas);
    } catch (error) {
        console.error("Error:", error);
    }
}

function mostrarCanciones(lista) {
    let contenedor = document.getElementById("contenedor-musica");
    contenedor.innerHTML = "";

    lista.forEach((c, index) => {
        let article = document.createElement("article");
        article.className = "music-card";
        
        article.innerHTML = `
            <figure>
                <img src="${c.portada}" onerror="this.src='img/default.jpg'">
            </figure>
            <p><strong>${c.nombre}</strong></p>
            <p>${c.artista}</p>
            <button onclick="reproducir(${index})">▶</button>
            ${playlistActual ? '' : `<button onclick="mostrarDialogoAgregarPlaylist(${c.id}, '${c.nombre}')">+ Playlist</button>`}
        `;
        
        contenedor.appendChild(article);
    });
}

function mostrarDialogoAgregarPlaylist(idCancion, nombreCancion) {
    if (playlists.length === 0) {
        alert("Primero crea una playlist");
        mostrarDialogoCrearPlaylist();
        return;
    }
    
    let mensaje = `Agregar "${nombreCancion}" a playlist:\n`;
    playlists.forEach((p, i) => {
        mensaje += `${i+1}. ${p.nombre}\n`;
    });
    mensaje += "\nEscribe el numero de la playlist:";
    
    let seleccion = prompt(mensaje);
    
    if (seleccion) {
        let index = parseInt(seleccion) - 1;
        if (index >= 0 && index < playlists.length) {
            agregarCancionAPlaylist(playlists[index].id, idCancion);
        } else {
            alert("Seleccion invalida");
        }
    }
}

async function agregarCancionAPlaylist(idPlaylist, idCancion) {
    try {
        let formData = new FormData();
        formData.append('id_playlist', idPlaylist);
        formData.append('id_cancion', idCancion);
        
        let res = await fetch("agregar_a_playlist.php", {
            method: 'POST',
            body: formData
        });
        
        let data = await res.json();
        
        if (data.success) {
            alert("Cancion agregada a la playlist");
        } else {
            alert("Error: " + data.error);
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Error al agregar a playlist");
    }
}

function mostrarDialogoCrearPlaylist() {
    let nombre = prompt("Nombre de la nueva playlist:");
    
    if (nombre && nombre.trim()) {
        crearPlaylist(nombre.trim());
    }
}

async function crearPlaylist(nombre) {
    try {
        let formData = new FormData();
        formData.append('nombre', nombre);
        
        let res = await fetch("crear_playlist.php", {
            method: 'POST',
            body: formData
        });
        
        let data = await res.json();
        
        if (data.success) {
            alert(`Playlist "${nombre}" creada`);
            cargarPlaylists();
        } else {
            alert("Error: " + data.error);
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Error al crear playlist");
    }
}

function reproducir(index) {
    if (!cancionesFiltradas[index]) return;
    indiceActual = index;
    let c = cancionesFiltradas[index];
    document.getElementById("nombre-cancion").textContent = c.nombre;
    document.getElementById("artista-cancion").textContent = c.artista;
    player.src = c.audio;
    player.play();
    btn.textContent = "⏸";
}

function siguiente() {
    if (cancionesFiltradas.length === 0) return;
    indiceActual = (indiceActual + 1) % cancionesFiltradas.length;
    reproducir(indiceActual);
}

function anterior() {
    if (cancionesFiltradas.length === 0) return;
    indiceActual = (indiceActual - 1 + cancionesFiltradas.length) % cancionesFiltradas.length;
    reproducir(indiceActual);
}

function togglePlay() {
    if (!player.src) return;
    if (player.paused) {
        player.play();
        btn.textContent = "⏸";
    } else {
        player.pause();
        btn.textContent = "▶";
    }
}

function buscar(texto) {
    texto = texto.toLowerCase();
    let base = playlistActual ? cancionesFiltradas : canciones;
    cancionesFiltradas = base.filter(c =>
        c.nombre.toLowerCase().includes(texto) ||
        c.artista.toLowerCase().includes(texto)
    );
    mostrarCanciones(cancionesFiltradas);
}

document.addEventListener("DOMContentLoaded", () => {
    player = document.getElementById("player-audio");
    barra = document.getElementById("barra");
    btn = document.getElementById("btn-play");
    
    cargarCanciones();
    cargarPlaylists();
    
    document.getElementById("busqueda").addEventListener("input", e => {
        buscar(e.target.value);
    });
    
    player.addEventListener("ended", siguiente);
    player.addEventListener("timeupdate", () => {
        if (player.duration) {
            barra.max = player.duration;
            barra.value = player.currentTime;
        }
    });
    
    barra.addEventListener("input", () => {
        player.currentTime = barra.value;
    });
});