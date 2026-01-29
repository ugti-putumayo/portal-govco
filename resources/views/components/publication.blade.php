<div class="publications-section">
    <h2>Publicaciones</h2>
    <div id="publications-container" class="publications-container"></div>
</div>

<script>
    // Función para obtener el type_id según la ruta o un parámetro de sección
    function getTypeIdBySection(section) {
        const typeMap = {
            'noticias': 1,   // Por ejemplo, 'noticias' corresponde a type_id = 1
            'articulos': 2,  // 'articulos' corresponde a type_id = 2
            // Agrega otros mapeos según tus necesidades
        };
        
        return typeMap[section] || null;
    }

    // Función principal para cargar y filtrar publicaciones
    function loadPublicationsBySection() {
        // Detecta la ruta actual o determina la sección basada en la URL
        const path = window.location.pathname;
        
        // Asigna el nombre de la sección basado en la URL
        let section = '';
        if (path.includes('noticias')) {
            section = 'noticias';
        } else if (path.includes('articulos')) {
            section = 'articulos';
        }
        // Agrega otras condiciones si es necesario

        // Obtiene el type_id basado en la sección detectada
        const typeId = getTypeIdBySection(section);

        if (typeId !== null && typeof api !== 'undefined') {
            api.get('/publications')
                .then(response => {
                    const publications = response.data;
                    const publicationsContainer = document.getElementById('publications-container');
                    
                    publicationsContainer.innerHTML = '';

                    // Filtra las publicaciones según el typeId
                    const filteredPublications = publications.filter(publication => publication.type_id === typeId);
                    
                    if (filteredPublications.length > 0) {
                        filteredPublications.forEach(publication => {
                            let publicationItem = document.createElement('div');
                            publicationItem.classList.add('publication-item');
                            
                            // Título de la publicación
                            let title = document.createElement('h3');
                            title.textContent = publication.title;
                            publicationItem.appendChild(title);

                            // Imagen de la publicación, si está disponible
                            if (publication.image) {
                                let image = document.createElement('img');
                                image.src = publication.image;
                                image.alt = publication.title;
                                publicationItem.appendChild(image);
                            }

                            // Descripción de la publicación
                            let description = document.createElement('p');
                            description.textContent = publication.description;
                            publicationItem.appendChild(description);

                            // Documento de la publicación, si está disponible
                            if (publication.document) {
                                let documentLink = document.createElement('a');
                                documentLink.href = publication.document;
                                documentLink.textContent = 'Ver documento';
                                documentLink.classList.add('document-link');
                                publicationItem.appendChild(documentLink);
                            }

                            // Enlace de "Leer más"
                            let readMore = document.createElement('a');
                            readMore.href = publication.url;
                            readMore.textContent = 'Leer más';
                            readMore.classList.add('read-more');
                            publicationItem.appendChild(readMore);

                            publicationsContainer.appendChild(publicationItem);
                        });
                    } else {
                        publicationsContainer.innerHTML = '<p>No se encontraron publicaciones para esta sección.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error al cargar las publicaciones:', error);
                    const publicationsContainer = document.getElementById('publications-container');
                    publicationsContainer.innerHTML = '<p>No se pudieron cargar las publicaciones en este momento.</p>';
                });
        } else {
            console.error('Error: api no está definido o no se ha encontrado typeId.');
        }
    }

    // Llama a la función para cargar las publicaciones filtradas por la sección actual
    loadPublicationsBySection();
</script>

<style>
    .publications-section {
        padding: 20px;
        background-color: #f9f9f9;
    }

    .publication-item {
        border-bottom: 1px solid #ddd;
        padding: 10px 0;
    }

    .publication-item h3 {
        font-size: 1.2em;
        margin: 0 0 5px;
    }

    .publication-item p {
        margin: 0 0 10px;
    }

    .read-more, .document-link {
        color: #007bff;
        text-decoration: none;
        font-weight: bold;
        display: block;
        margin-top: 5px;
    }

    .read-more:hover, .document-link:hover {
        text-decoration: underline;
    }

    .publication-item img {
        max-width: 100%;
        height: auto;
        margin-top: 10px;
    }
</style>
