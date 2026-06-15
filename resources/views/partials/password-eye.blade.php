{{-- Bascule afficher/masquer le mot de passe (œil) — demandé par Denis.
     L'œil est positionné À L'INTÉRIEUR du champ, à droite. Vanilla JS (build gelée). --}}
<style>
    .pw-wrap { position: relative !important; display: block !important; width: 100%; }
    .pw-wrap > input { width: 100% !important; box-sizing: border-box !important; padding-right: 2.6em !important; }
    .pw-wrap > .pw-eye {
        position: absolute !important;
        right: .55em; top: 0; bottom: 0; margin: auto 0;
        height: 1.7em; width: 1.9em;
        display: inline-flex; align-items: center; justify-content: center;
        background: none; border: 0; padding: 0; cursor: pointer;
        color: #69716b; z-index: 5; line-height: 1;
    }
    .pw-wrap > .pw-eye:hover { color: #157a47; }
</style>
<script>
    (function () {
        function enhance(input) {
            if (input.dataset.pwEnhanced) { return; }
            input.dataset.pwEnhanced = '1';

            var wrap = document.createElement('span');
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
