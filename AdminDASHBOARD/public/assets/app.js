function validateUserForm(){
    const name=document.querySelector('[name=name]').value.trim();
    const email=document.querySelector('[name=email]').value.trim();
    const pass=document.querySelector('[name=password]').value;
    if(name.length<2){alert('Name is required');return false;}
    if(!email.includes('@')){alert('Valid email is required');return false;}
    if(pass.length<8){alert('Password must be at least 8 characters');return false;}
    return true;
}
function validatePostForm(form){
    if(form.title.value.trim()==='' || form.country.value.trim()===''){
        alert('Title and country required'); return false;
    }
    return true;
}
function apiAction(action,id){
    if(!confirm('Are you sure?')) return;
    fetch('/api/admin_action.php?action='+action,{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded','X-CSRF-Token':document.querySelector('[name=csrf_token]')?.value || ''},
        body:'id='+encodeURIComponent(id)
    }).then(r=>r.json()).then(data=>{
        if(data.ok){ location.reload(); } else { alert(data.message || 'Action failed'); }
    }).catch(()=>alert('Server error'));
}
