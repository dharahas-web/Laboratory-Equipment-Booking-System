document.querySelectorAll('[data-confirm]').forEach(el=>el.addEventListener('click',e=>{if(!confirm(el.dataset.confirm)){e.preventDefault();}}));
document.querySelectorAll('[data-toggle-password]').forEach(btn=>btn.addEventListener('click',()=>{const input=document.getElementById(btn.dataset.togglePassword);if(input)input.type=input.type==='password'?'text':'password';}));
