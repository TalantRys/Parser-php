document.addEventListener('DOMContentLoaded', () => {
  const btnUpdate = document.querySelector('.team__button');
  // ОБНОВЛЯЕМ ДАННЫЕ И ТАБЛИЦУ БЕЗ ПЕРЕЗАГРУЗКИ
  btnUpdate.addEventListener('click', updateTable);
})
async function updateTable(e) {
  e.preventDefault;
  const tableBody = document.querySelector('.team-table tbody');
  e.target.classList.add('_loading');
  tableBody.classList.add('_loading');
  try {
    let response = await fetch('vendor/action/parse.php');
    if (response.ok) {
      let result = await response.json();
      alert('Обновление прошло успешно!');
      tableBody.innerHTML = toHtml(result.team);
      e.target.classList.remove('_loading');
      tableBody.classList.remove('_loading');
    } else {
      alert(response.statusText);
      e.target.classList.remove('_loading');
      tableBody.classList.remove('_loading');
    }
  } catch (error) {
    console.log(error);
    e.target.classList.remove('_loading');
    tableBody.classList.remove('_loading');
  }
}
// ДЕКОДИРУЕМ ДАННЫЕ В HTML
function toHtml(json) {
  let html = '';
  for (let k = 0; k < json.length; k++) {
    const key = json[k];
    html +=
    `<tr>
      <td>${key['id']}</td>
      <td class="team-table__link" colspan="2">
        <a href="players.php?id=${key['id']}">
          <div class="team-table__img img">
            <img alt="team icon" src="${key['icon_link']}">
          </div>
          <p>${key['name']}</p>
        </a>
      </td>
      <td>${key['games']}</td>
      <td>${key['win']}</td>
      <td>${key['draw']}</td>
      <td>${key['lost']}</td>
    </tr>`;
  }
  return html;
}