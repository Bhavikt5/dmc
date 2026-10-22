/*!
 * DMC Nuts — Three.js scenes
 * - [data-dmc-nuts]      : floating 3D almonds / cashews / pistachios (homepage hero)
 * - [data-dmc-particles] : soft drifting golden dust (about page)
 * Requires THREE (UMD build). GSAP is optional (used for intro + scroll drift).
 */
(function () {
  "use strict";
  if (typeof THREE === "undefined") return;

  var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var isSmall = window.matchMedia("(max-width: 767px)").matches;

  /* ---------- helpers ---------- */

  function rand(min, max) {
    return min + Math.random() * (max - min);
  }

  // Run a render loop only while the canvas is on screen.
  function visibleLoop(el, tick) {
    var running = false;
    var raf = 0;
    function frame(t) {
      tick(t);
      raf = requestAnimationFrame(frame);
    }
    var io = new IntersectionObserver(function (entries) {
      var on = entries[0].isIntersecting;
      if (on && !running) {
        running = true;
        raf = requestAnimationFrame(frame);
      } else if (!on && running) {
        running = false;
        cancelAnimationFrame(raf);
      }
    });
    io.observe(el);
  }

  function makeRenderer(host) {
    var renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: "high-performance" });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.75));
    renderer.setSize(host.clientWidth, host.clientHeight, false);
    if ("outputEncoding" in renderer) renderer.outputEncoding = THREE.sRGBEncoding;
    renderer.domElement.className = "dmc-canvas";
    host.appendChild(renderer.domElement);
    return renderer;
  }

  function onResize(host, renderer, camera) {
    var ro = new ResizeObserver(function () {
      var w = host.clientWidth;
      var h = host.clientHeight;
      if (!w || !h) return;
      renderer.setSize(w, h, false);
      camera.aspect = w / h;
      camera.updateProjectionMatrix();
    });
    ro.observe(host);
  }

  // Procedural skin texture: base colour + long grooves (almond) or soft mottling.
  function skinTexture(base, groove, streaks) {
    var c = document.createElement("canvas");
    c.width = 512;
    c.height = 512;
    var g = c.getContext("2d");
    g.fillStyle = base;
    g.fillRect(0, 0, 512, 512);
    for (var i = 0; i < 2600; i++) {
      g.fillStyle = "rgba(255,255,255," + rand(0, 0.06) + ")";
      g.fillRect(rand(0, 512), rand(0, 512), rand(1, 3), rand(1, 3));
    }
    if (streaks) {
      g.strokeStyle = groove;
      for (var s = 0; s < 140; s++) {
        var x = rand(0, 512);
        g.globalAlpha = rand(0.15, 0.55);
        g.lineWidth = rand(0.6, 2.4);
        g.beginPath();
        g.moveTo(x, 0);
        g.bezierCurveTo(x + rand(-14, 14), 170, x + rand(-14, 14), 340, x + rand(-8, 8), 512);
        g.stroke();
      }
      g.globalAlpha = 1;
    }
    var tex = new THREE.CanvasTexture(c);
    if ("encoding" in tex) tex.encoding = THREE.sRGBEncoding;
    tex.anisotropy = 4;
    return tex;
  }

  /* ---------- nut geometry ---------- */

  function almondGeometry() {
    var geo = new THREE.SphereGeometry(1, 64, 48);
    var p = geo.attributes.position;
    var v = new THREE.Vector3();
    for (var i = 0; i < p.count; i++) {
      v.fromBufferAttribute(p, i);
      var y = v.y; // -1..1 along length
      // Teardrop: fuller at the base, pointed at the tip.
      var taper = y > 0 ? 1 - Math.pow(y, 1.6) * 0.55 : 1 - Math.pow(-y, 2.2) * 0.12;
      v.x *= 0.62 * taper;
      v.z *= 0.36 * taper;
      v.y *= 1.05;
      // Slight bend so it doesn't look machine-made.
      v.z += 0.06 * (1 - y * y);
      p.setXYZ(i, v.x, v.y, v.z);
    }
    geo.computeVertexNormals();
    return geo;
  }

  function cashewGeometry() {
    var R = 0.78;
    var arc = Math.PI * 1.15;
    var geo = new THREE.TorusGeometry(R, 0.3, 32, 72, arc);
    var p = geo.attributes.position;
    var v = new THREE.Vector3();
    var c = new THREE.Vector3();
    for (var i = 0; i < p.count; i++) {
      v.fromBufferAttribute(p, i);
      var u = Math.atan2(v.y, v.x);
      if (u < -0.001) u += Math.PI * 2;
      var t = Math.min(Math.max(u / arc, 0), 1);
      c.set(Math.cos(u) * R, Math.sin(u) * R, 0);
      // Plump kidney profile: thick in the middle, rounded ends.
      // Tapers to closed, rounded tips; the inner tip (t≈1) is a little slimmer.
      var f = Math.pow(Math.sin(Math.PI * Math.min(Math.max(t, 0.015), 0.985)), 0.45) * (1 - 0.18 * t);
      v.sub(c).multiplyScalar(f).add(c);
      v.z *= 0.85;
      p.setXYZ(i, v.x, v.y, v.z);
    }
    geo.computeVertexNormals();
    geo.center();
    return geo;
  }

  function pistachioGroup(shellMat, kernelMat) {
    var group = new THREE.Group();
    var shellGeo = new THREE.SphereGeometry(1, 48, 32, 0, Math.PI * 2, 0, Math.PI);
    shellGeo.scale(0.55, 0.78, 0.5);
    var shell = new THREE.Mesh(shellGeo, shellMat);
    group.add(shell);
    var kernelGeo = new THREE.SphereGeometry(1, 32, 24);
    kernelGeo.scale(0.36, 0.6, 0.3);
    var kernel = new THREE.Mesh(kernelGeo, kernelMat);
    kernel.position.set(0, 0.04, 0.3);
    group.add(kernel);
    return group;
  }

  /* ---------- hero: floating nuts ---------- */

  function initNuts(host) {
    var scene = new THREE.Scene();
    var camera = new THREE.PerspectiveCamera(35, host.clientWidth / host.clientHeight, 0.1, 100);
    camera.position.set(0, 0, 14);

    var renderer = makeRenderer(host);
    onResize(host, renderer, camera);

    scene.add(new THREE.HemisphereLight(0xfff4e2, 0x4e2320, 0.85));
    var key = new THREE.DirectionalLight(0xffe2b8, 1.25);
    key.position.set(4, 6, 8);
    scene.add(key);
    var rim = new THREE.DirectionalLight(0x9fd38a, 0.45);
    rim.position.set(-6, -2, -4);
    scene.add(rim);

    var almondMat = new THREE.MeshStandardMaterial({
      map: skinTexture("#b9783f", "#6e3b1c", true),
      roughness: 0.78,
      metalness: 0.02,
    });
    var cashewMat = new THREE.MeshStandardMaterial({
      map: skinTexture("#e6c690", "#d6b27a", false),
      roughness: 0.62,
      metalness: 0.0,
    });
    var shellMat = new THREE.MeshStandardMaterial({
      map: skinTexture("#e9dbbd", "#c9b48a", false),
      roughness: 0.55,
      side: THREE.DoubleSide,
    });
    var kernelMat = new THREE.MeshStandardMaterial({ color: 0x7fae3b, roughness: 0.6 });

    var almondGeo = almondGeometry();
    var cashewGeo = cashewGeometry();

    var count = isSmall ? 9 : 16;
    var nuts = [];
    var root = new THREE.Group();
    scene.add(root);

    for (var i = 0; i < count; i++) {
      var kind = i % 5;
      var mesh;
      if (kind === 1 || kind === 3) mesh = new THREE.Mesh(cashewGeo, cashewMat);
      else if (kind === 4) mesh = pistachioGroup(shellMat, kernelMat);
      else mesh = new THREE.Mesh(almondGeo, almondMat);

      // Keep the headline area clear: desktop clusters on the right, mobile along the top band.
      var x, y, z;
      if (isSmall) {
        // Portrait card: the top ~210px is reserved for the nuts (see .dmc-hero__main padding).
        x = rand(-2.4, 2.4);
        y = rand(2.9, 4.6);
        z = rand(-3, 1);
      } else {
        x = rand(2.4, 6.6);
        y = rand(-3.8, 3.8);
        z = rand(-4, 2);
        if (i < 2) x = rand(-7.6, -6.8); // a couple peeking from the left edge
      }
      mesh.position.set(x, y, z);
      mesh.rotation.set(rand(0, Math.PI), rand(0, Math.PI), rand(0, Math.PI));
      var s = rand(0.55, 1.0) * (kind === 1 || kind === 3 ? 0.8 : 1);
      mesh.scale.setScalar(s);
      mesh.userData = {
        base: mesh.position.clone(),
        spin: new THREE.Vector3(rand(-0.25, 0.25), rand(-0.35, 0.35), rand(-0.2, 0.2)),
        bob: rand(0.2, 0.55),
        phase: rand(0, Math.PI * 2),
        scale: s,
      };
      root.add(mesh);
      nuts.push(mesh);
    }

    var pointer = { x: 0, y: 0, tx: 0, ty: 0 };
    window.addEventListener(
      "pointermove",
      function (e) {
        pointer.tx = (e.clientX / window.innerWidth) * 2 - 1;
        pointer.ty = (e.clientY / window.innerHeight) * 2 - 1;
      },
      { passive: true }
    );

    // Scroll progress through the hero (0..1) pushes the cluster upward & apart.
    var scroll = { p: 0 };
    if (window.gsap && window.ScrollTrigger) {
      gsap.to(scroll, {
        p: 1,
        ease: "none",
        scrollTrigger: { trigger: host.closest("section") || host, start: "top top", end: "bottom top", scrub: true },
      });
      // Intro: nuts pop in with a stagger.
      nuts.forEach(function (n, i) {
        n.scale.setScalar(0.0001);
        gsap.to(n.scale, {
          x: n.userData.scale,
          y: n.userData.scale,
          z: n.userData.scale,
          duration: 1.4,
          delay: 0.35 + i * 0.06,
          ease: "elastic.out(1, 0.6)",
        });
      });
    }

    var clock = new THREE.Clock();
    function tick() {
      var dt = Math.min(clock.getDelta(), 0.05);
      var t = clock.elapsedTime;
      pointer.x += (pointer.tx - pointer.x) * 0.04;
      pointer.y += (pointer.ty - pointer.y) * 0.04;
      root.rotation.y = pointer.x * 0.18;
      root.rotation.x = pointer.y * 0.1;

      for (var i = 0; i < nuts.length; i++) {
        var n = nuts[i];
        var d = n.userData;
        if (!reduceMotion) {
          n.rotation.x += d.spin.x * dt;
          n.rotation.y += d.spin.y * dt;
          n.rotation.z += d.spin.z * dt;
        }
        n.position.y = d.base.y + Math.sin(t * 0.8 + d.phase) * d.bob + scroll.p * (2.5 + d.base.z * 0.4);
        n.position.x = d.base.x * (1 + scroll.p * 0.35);
      }
      renderer.render(scene, camera);
    }
    if (reduceMotion) tick();
    else visibleLoop(host, tick);
  }

  /* ---------- about: drifting dust particles ---------- */

  function dotTexture() {
    var c = document.createElement("canvas");
    c.width = c.height = 64;
    var g = c.getContext("2d");
    var grad = g.createRadialGradient(32, 32, 0, 32, 32, 32);
    grad.addColorStop(0, "rgba(255,255,255,1)");
    grad.addColorStop(0.4, "rgba(255,255,255,0.5)");
    grad.addColorStop(1, "rgba(255,255,255,0)");
    g.fillStyle = grad;
    g.fillRect(0, 0, 64, 64);
    return new THREE.CanvasTexture(c);
  }

  function initParticles(host) {
    var scene = new THREE.Scene();
    var camera = new THREE.PerspectiveCamera(60, host.clientWidth / host.clientHeight, 0.1, 100);
    camera.position.z = 10;
    var renderer = makeRenderer(host);
    onResize(host, renderer, camera);

    var n = isSmall ? 260 : 650;
    var pos = new Float32Array(n * 3);
    var col = new Float32Array(n * 3);
    var palette = [new THREE.Color("#e3b277"), new THREE.Color("#c8894a"), new THREE.Color("#8cc56a"), new THREE.Color("#fff3dc")];
    for (var i = 0; i < n; i++) {
      pos[i * 3] = rand(-12, 12);
      pos[i * 3 + 1] = rand(-7, 7);
      pos[i * 3 + 2] = rand(-6, 4);
      var c = palette[(Math.random() * palette.length) | 0];
      col[i * 3] = c.r;
      col[i * 3 + 1] = c.g;
      col[i * 3 + 2] = c.b;
    }
    var geo = new THREE.BufferGeometry();
    geo.setAttribute("position", new THREE.BufferAttribute(pos, 3));
    geo.setAttribute("color", new THREE.BufferAttribute(col, 3));
    var mat = new THREE.PointsMaterial({
      size: 0.14,
      map: dotTexture(),
      vertexColors: true,
      transparent: true,
      opacity: 0.85,
      depthWrite: false,
      blending: THREE.AdditiveBlending,
    });
    var points = new THREE.Points(geo, mat);
    scene.add(points);

    var mx = 0,
      my = 0;
    window.addEventListener(
      "pointermove",
      function (e) {
        mx = (e.clientX / window.innerWidth - 0.5) * 0.6;
        my = (e.clientY / window.innerHeight - 0.5) * 0.4;
      },
      { passive: true }
    );

    var clock = new THREE.Clock();
    function tick() {
      var t = clock.getElapsedTime();
      var a = geo.attributes.position.array;
      for (var i = 0; i < n; i++) {
        a[i * 3 + 1] += 0.004 + (i % 7) * 0.0006;
        a[i * 3] += Math.sin(t * 0.3 + i) * 0.0015;
        if (a[i * 3 + 1] > 7) a[i * 3 + 1] = -7;
      }
      geo.attributes.position.needsUpdate = true;
      points.rotation.y += (mx - points.rotation.y) * 0.03;
      points.rotation.x += (my - points.rotation.x) * 0.03;
      renderer.render(scene, camera);
    }
    if (reduceMotion) tick();
    else visibleLoop(host, tick);
  }

  function boot() {
    document.querySelectorAll("[data-dmc-nuts]").forEach(initNuts);
    document.querySelectorAll("[data-dmc-particles]").forEach(initParticles);
  }
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
  else boot();
})();
