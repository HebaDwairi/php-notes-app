
const loadMoreBtn = document.querySelector('#load-more-btn');
const notesList = document.querySelector('#notes-list');
const notesItems = notesList.querySelectorAll('li');
const noNotesMessage = document.querySelector('#no-notes-message');
const page = document.body.dataset.page;
const isHomepage = (page === "home");

let lastUpdatedAt = null;
let lastId = null;



if(notesItems.length < 10) {
   loadMoreBtn.style.display = 'none';
}

if(noNotesMessage) {
  if(notesItems.length === 0) {
    noNotesMessage.classList.remove('hidden');
  }
  else {
    noNotesMessage.classList.add('hidden');
  }
}


const lastNote = notesList?.lastElementChild;
if(lastNote) {
  lastUpdatedAt = lastNote.dataset.updatedAt;
  lastId = lastNote.dataset.id;
  console.log(lastUpdatedAt, lastId);
} 

loadMoreBtn?.addEventListener('click', async () => {
  if (!notesList) return;

  loadMoreBtn.disabled = true;
  loadMoreBtn.textContent = 'Loading...';

  let url = `/load_more.php?limit=10&is_homepage=${isHomepage}`;
  if (lastUpdatedAt && lastId) {
    url += `&updated_at=${encodeURIComponent(lastUpdatedAt)}&id=${lastId}`;
  }

  try {
    const response = await fetch(url);
    if (!response.ok) throw new Error('Failed to load notes');

    const data = await response.json();
    const notes = data.notes;

    if (!data.success || !notes || notes.length === 0) {
      loadMoreBtn.style.display = 'none';
      return;
    }

    notes.forEach(note => {
      const li = document.createElement('li');
      li.className = 'bg-slate-800/50 rounded-xl border border-transparent hover:text-accent hover:border-accent transition-colors duration-300 group overflow-hidden';

      li.innerHTML = `
        <a href="/notes/${note.slug}" class="block">
          ${note.image_path ? `<img src="${note.image_path}" alt="${note.title}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300 rounded-t-xl">` : ''}
          <div class='flex justify-between items-center p-4'>
            <div>
              <strong>${note.title}</strong>
            </div>
          </div>
        </a>
        <p class='text-sm text-slate-300 p-4'>${note.content}</p>
        <div class="flex justify-between items-center mt-5 px-4 pb-4">
          <p class='text-xs text-slate-500'>Edited: ${note.updated_at}</p>
          <p class='text-xs text-slate-500'>Created: ${note.created_at}</p>
        </div>
      `;

      notesList.appendChild(li);
    });

    const lastNote = notes[notes.length - 1];
    lastUpdatedAt = lastNote.updated_at;
    lastId = lastNote.id;


    if (notes.length < 10) {
      loadMoreBtn.style.display = 'none';
    } else {
      loadMoreBtn.disabled = false;
      loadMoreBtn.textContent = 'Load More';
    }

  } catch (err) {
    console.log(err.message);
    loadMoreBtn.disabled = false;
    loadMoreBtn.textContent = 'Load More';
  }
});
