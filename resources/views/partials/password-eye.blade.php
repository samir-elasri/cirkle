{{-- Bascule afficher/masquer le mot de passe (œil) — demandé par Denis.
     Vanilla JS, build compilée gelée. Améliore tous les input[type=password] de la page. --}}
<style>
    .pw-wrap { position: relative; display: block; }
    .pw-wrap > input { width: 100%; padding-right: 2.4em; }
    .pw-eye {
        position: absolute; right: .6em; top: 50%; transform: translateY(-50%);
        background: none; border: 0; cursor: pointer; color: #69716b; padding: 0; line-height: 1;
    }
    .pw-eye:hover { color: #157a47; }
</style>
<script>
    (function () {
        function enhance(input) {
            if (input.dataset.pwEnhanced) { return; }
            input.dataset.pwEnhanced = '1';

            var wrap = document.createElement('div');
            wrap.className = 'pw-wrap';
            input.parentNode.insertBefore(wrap, input);
            wrap.appendChild(input);

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pw-eye';
            btn.setAttribute('aria-label', @json(__('auth.register.toggle_password')));
            btn.innerHTML = '<i class="fas fa-eye"></i>';
            btn.addEventListener('click', function () {
                var hidden = input.type === 'password';
                input.type = hidden ? 'text' : 'password';
                btn.innerHTML = '<i class="fas fa-eye' + (hidden ? '-slash' : '') + '"></i>';
            });
            wrap.appendChild(btn);
        }

        document.querySelectorAll('input[type=password]').forEach(enhance);
    })();
</script>
