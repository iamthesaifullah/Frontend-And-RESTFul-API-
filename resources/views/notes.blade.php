<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes Manager</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        button {
            padding: 5px 10px;
            margin: 2px;
        }

        input {
            padding: 5px;
            margin: 5px;
        }
    </style>
</head>
<body>
    <h2>Notes Manager</h2>
    <input type="text" id="title" placeholder="Title">
    <input type="text" id="content" placeholder="Content">
    <button onclick="createNote()">Add Note</button>
    <br><br>
    <input type="text" id="searchId" placeholder="Search by ID">
    <button onclick="searchNote()">Search</button>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Content</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="notesTable"></tbody>
    </table>

    <script>
        const API_BASE = 'http://127.0.0.1:8000/api/notes';

        function fetchNotes() {
            $.ajax({
                url: API_BASE,
                type: 'GET',
                success: function(notes) {
                    let tableBody = $('#notesTable');
                    tableBody.empty();
                    notes.forEach(note => {
                        tableBody.append(`
                            <tr>
                                <td>${note.id}</td>
                                <td>${note.title}</td>
                                <td>${note.content}</td>
                                <td>
                                    <button onclick="deleteNote(${note.id})">Delete</button>
                                    <button onclick="enableUpdate(${note.id})">Edit</button>
                                </td>
                            </tr>
                        `);
                    });
                },
                error: function(xhr) {
                    console.log('Error fetching notes:', xhr);
                }
            });
        }

        function createNote() {
            $.ajax({
                url: API_BASE,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    title: $('#title').val(),
                    content: $('#content').val()
                }),
                success: function() {
                    fetchNotes();
                    $('#title, #content').val('');
                },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.error || 'Creation failed'));
                }
            });
        }

        function enableUpdate(id) {
            $.ajax({
                url: `${API_BASE}/${id}`,
                type: 'GET',
                success: function(note) {
                    let row = $(`tr:has(td:first-child:contains('${id}'))`);
                    row.html(`
                        <td>${note.id}</td>
                        <td><input type="text" id="edit-title-${note.id}" value="${note.title}"></td>
                        <td><input type="text" id="edit-content-${note.id}" value="${note.content}"></td>
                        <td>
                            <button onclick="submitUpdate(${note.id})">Save</button>
                            <button onclick="fetchNotes()">Cancel</button>
                        </td>
                    `);
                }
            });
        }

        function submitUpdate(id) {
            $.ajax({
                url: `${API_BASE}/${id}`,
                type: 'PUT',
                contentType: 'application/json',
                data: JSON.stringify({
                    title: $(`#edit-title-${id}`).val(),
                    content: $(`#edit-content-${id}`).val()
                }),
                success: fetchNotes,
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.error || 'Update failed'));
                }
            });
        }

        function deleteNote(id) {
            if (!confirm('Delete this note?')) return;
            $.ajax({
                url: `${API_BASE}/${id}`,
                type: 'DELETE',
                success: fetchNotes,
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.error || 'Deletion failed'));
                }
            });
        }

        function searchNote() {
            const id = $('#searchId').val();
            if (!id) return fetchNotes();

            $.ajax({
                url: `${API_BASE}/${id}`,
                type: 'GET',
                success: function(note) {
                    $('#notesTable').html(`
                        <tr>
                            <td>${note.id}</td>
                            <td>${note.title}</td>
                            <td>${note.content}</td>
                            <td>
                                <button onclick="deleteNote(${note.id})">Delete</button>
                                <button onclick="enableUpdate(${note.id})">Edit</button>
                            </td>
                        </tr>
                    `);
                },
                error: function() {
                    alert('Note not found');
                    fetchNotes();
                }
            });
        }

        fetchNotes(); // Initial load
    </script>
</body>
</html>
