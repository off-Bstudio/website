function toggleEdit(id){
  var row = document.getElementById('edit-' + id);
  if(!row) return;
  row.classList.toggle('open');
}

document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('form[data-confirm]').forEach(function(form){
    form.addEventListener('submit', function(e){
      var msg = form.getAttribute('data-confirm');
      if(!confirm(msg)){ e.preventDefault(); }
    });
  });

  document.querySelectorAll('.flash').forEach(function(el){
    setTimeout(function(){ el.style.display = 'none'; }, 6000);
  });
});
