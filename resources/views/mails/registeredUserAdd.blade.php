<!DOCTYPE html>
<html>
<head>
    <title>Ajout au groupe</title>
</head>
<body>
    <h1>Bonjour {{ $user }},</h1>
    <p>Vous avez été ajouté au groupe "{{ $group }}" sur notre plateforme.</p>
    <p>Connectez-vous pour voir les détails du groupe et participer aux discussions.</p>
    <p><a href="">Accéder au groupe</a></p>
    {{-- {{ route('groups.show', $group->id) }} --}}
</body>
</html>
