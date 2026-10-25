(function () {
    // Este archivo se comparte entre los cuatro paneles para no repetir el menú de cuenta.
    window.databyteText = (espanol, ingles) => document.documentElement.lang === 'en' ? ingles : espanol;
    const apiUrl = new URL('../../API_Usuarios/public/index.php', window.location.href).toString();
    document.body.insertAdjacentHTML('afterbegin', `
        <header class="app-header">
            <div class="app-header-inner">
                <a class="app-brand" href="../LandingPage/DataByte_LandingPage/LandingPage.html" aria-label="DataByte, inicio">
                    <img src="../../assets/logo.png" alt="">
                    <span>DataByte</span>
                </a>
                <div class="profile-control">
                    <button type="button" class="profile-trigger" id="profileTrigger" aria-expanded="false" aria-controls="profileMenu">
                        <img class="profile-logo" src="../../assets/logo.png" alt="DataByte">
                        <span class="profile-trigger-name" id="profileName">Mi cuenta</span>
                        <span aria-hidden="true">⌄</span>
                    </button>
                    <div class="profile-menu" id="profileMenu" hidden>
                        <button type="button" id="openProfile">Ver mis datos y editarlos</button>
                        <button type="button" id="logoutButton">Cerrar sesión</button>
                    </div>
                </div>
            </div>
        </header>
        <dialog class="perfil-dialog" id="profileDialog" aria-labelledby="profileTitle">
            <div class="perfil-header"><h2 id="profileTitle">Mis datos</h2></div>
            <form class="perfil-form" id="profileForm">
                <div class="campo"><label for="profileCedula">Cédula</label><input id="profileCedula" readonly></div>
                <div class="campo"><label for="profileRole">Rol</label><input id="profileRole" readonly></div>
                <div class="campo"><label for="profileEmail">Correo electrónico</label><input id="profileEmail" type="email" maxlength="50" required autocomplete="email"></div>
                <div class="campo"><label for="profileFirstName">Nombre</label><input id="profileFirstName" type="text" maxlength="50" required autocomplete="given-name"></div>
                <div class="campo"><label for="profileLastName">Apellido</label><input id="profileLastName" type="text" maxlength="50" required autocomplete="family-name"></div>
                <h3 class="perfil-password-title">Cambiar contraseña (opcional)</h3>
                <div class="campo"><label for="profileCurrentPassword">Contraseña actual</label><input id="profileCurrentPassword" type="password" autocomplete="current-password"></div>
                <div class="campo"><label for="profileNewPassword">Nueva contraseña</label><input id="profileNewPassword" type="password" minlength="8" maxlength="72" autocomplete="new-password"></div>
                <div class="campo"><label for="profileConfirmPassword">Repetir nueva contraseña</label><input id="profileConfirmPassword" type="password" minlength="8" maxlength="72" autocomplete="new-password"></div>
                <p class="perfil-message" id="profileMessage" role="status"></p>
                <div class="perfil-actions">
                    <button type="button" class="cancelar" id="closeProfile">Cancelar</button>
                    <button type="submit">Guardar cambios</button>
                </div>
            </form>
        </dialog>
    `);

    const trigger = document.getElementById('profileTrigger');
    const menu = document.getElementById('profileMenu');
    const dialog = document.getElementById('profileDialog');
    const form = document.getElementById('profileForm');
    const message = document.getElementById('profileMessage');
    const passwordInputs = ['profileCurrentPassword', 'profileNewPassword', 'profileConfirmPassword'].map(id => document.getElementById(id));
    let profile = null;

    // Completa el menú y el formulario con los datos de la sesión actual.
    function setProfile(data) {
        profile = data;
        const fullName = `${data.PrNom || ''} ${data.PrApel || ''}`.trim();
        document.getElementById('profileName').textContent = fullName || data.email || 'Mi cuenta';
        document.getElementById('profileCedula').value = data.cedula || '';
        document.getElementById('profileRole').value = data.rol || '';
        document.getElementById('profileEmail').value = data.email || '';
        document.getElementById('profileFirstName').value = data.PrNom || '';
        document.getElementById('profileLastName').value = data.PrApel || '';
    }

    // La API devuelve el usuario asociado a la sesión iniciada.
    fetch(`${apiUrl}?accion=usuarioActual`, { credentials: 'same-origin' })
        .then(response => response.json().then(data => ({ response, data })))
        .then(({ response, data }) => { if (response.ok && data.usuario) setProfile(data.usuario); })
        .catch(() => {});

    trigger.addEventListener('click', () => {
        menu.hidden = !menu.hidden;
        trigger.setAttribute('aria-expanded', String(!menu.hidden));
    });
    document.addEventListener('click', event => {
        if (!event.target.closest('.profile-control')) {
            menu.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
        }
    });
    document.getElementById('openProfile').addEventListener('click', () => {
        menu.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
        message.textContent = '';
        form.reset();
        if (profile) setProfile(profile);
        dialog.showModal();
    });
    document.getElementById('closeProfile').addEventListener('click', () => dialog.close());

    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (!form.reportValidity()) return;
        const [currentPassword, newPassword, confirmPassword] = passwordInputs.map(input => input.value);
        const changingPassword = currentPassword || newPassword || confirmPassword;
        if (changingPassword && (!currentPassword || newPassword.length < 8 || newPassword !== confirmPassword)) {
            message.textContent = 'Para cambiar la contraseña, ingresá la actual y repetí una nueva de al menos 8 caracteres.';
            message.style.color = '#b42318';
            return;
        }
        message.textContent = 'Guardando…';
        message.style.color = '#40516d';
        try {
            // El servidor valida y guarda los cambios del perfil propio.
            const response = await fetch(`${apiUrl}?accion=miPerfil`, {
                method: 'PUT',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    email: document.getElementById('profileEmail').value.trim(),
                    PrNom: document.getElementById('profileFirstName').value.trim(),
                    PrApel: document.getElementById('profileLastName').value.trim(),
                    contrasena_actual: currentPassword,
                    contrasena_nueva: newPassword,
                    confirmar_contrasena: confirmPassword
                })
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'No se pudieron guardar los datos.');
            setProfile(data.usuario);
            passwordInputs.forEach(input => { input.value = ''; });
            message.textContent = data.message || 'Cambios guardados.';
            message.style.color = '#177245';
        } catch (error) {
            message.textContent = error.message;
            message.style.color = '#b42318';
        }
    });

    document.getElementById('logoutButton').addEventListener('click', async () => {
        try {
            // Cierra la sesión en el servidor antes de volver al login.
            await fetch(`${apiUrl}?accion=cerrarSesion`, { method: 'POST', credentials: 'same-origin' });
        } finally {
            window.location.replace('../Login/LandingPageLogin.html');
        }
    });
})();
