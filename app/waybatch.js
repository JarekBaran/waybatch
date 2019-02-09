`use strict`;

let totalLines, counter, outputCounter, failedCounter;

const modal = document.querySelector(`#modal`);
const modalTitle = document.querySelector(`#modal-title`);
const modalContent = document.querySelector(`#modal-content`);
const input = document.querySelector(`#input`);
const token = document.querySelector(`#token`);
const output = document.querySelector(`#output`);
const failed = document.querySelector(`#failed`);
const saveBtn = document.querySelector(`#save-btn`);
const checkBtn = document.querySelector(`#check-btn`);
const failedBtn = document.querySelector(`#failed-btn`);
const reportBtn = document.querySelector(`#report-btn`);
const archiveBtn = document.querySelector(`#archive-btn`);

const updateCounter = function(success = true) {
  counter++; (success) ? outputCounter++ : failedCounter++;
  archiveBtn.innerHTML = `Saved ${counter} / ${totalLines}`;
  if(counter == totalLines) {
    if(outputCounter != 0) {
      output.innerHTML = `<span class="badge badge-pill badge-warning">Correctly saved ${outputCounter} / ${totalLines}</span><br>${output.innerHTML}`;
      saveBtn.disabled = false;
    }
    if(failedCounter != 0) {
      checkBtn.disabled = false;
      failedBtn.disabled = false;
    }
    input.value = ``;
    archiveBtn.innerHTML = `Save in WebArchive`;
    archiveBtn.disabled = false;
    token.disabled = false;
    reportBtn.disabled = false;
  }
}

const ajax = function(url) {
  fetch(`app/waybatch.php?url=${punycode.toASCII(url)}`, {
    method: `GET`,
    credentials: `same-origin`
  })
  .then(response => response.json())
  .then(data => {
    let link = data.match(/\bhttps?:\/\/web\.archive\.org\/web\/\d{14}\/https?:\/\/\S+/gi);
    output.classList.add(`alert-success`);
    output.innerHTML = `<a href="${link}" target="_blank">${url}</a><br>${output.innerHTML}`;
    updateCounter();
  })
  .catch(error => {
    failed.classList.add(`alert-danger`);
    failed.innerHTML = `${url}<br>${failed.innerHTML}`;
    updateCounter(false);
  });
}

const archiveUrl = function(list = input.value) {
  if(list == ``) return;
  reportBtn.disabled = true;
  token.disabled = true;
  archiveBtn.disabled = true;
  archiveBtn.innerHTML = `Saving ...`;
  let urls = Array.from(new Set(list.replace(/[^\S\r\n]/g, '').split(`\n`).filter(Boolean)));
  totalLines = urls.length;
  counter = 0;
  outputCounter = 0;
  failedCounter = 0;
  for(let url of urls) {
    setTimeout(ajax(url), 300);
  }
}

const checkFailed = function() {
  let list = failed.innerHTML.replace(/<br>/g, `\n`);
  checkBtn.disabled = true;
  failedBtn.disabled = true;
  failed.classList.remove(`alert-danger`);
  failed.innerHTML = ``;
  input.value = list;
  archiveUrl(list);
}

const saveFile = function(list,name) {
  let date = new Date();
  let timestamp = `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}-${date.getTime()}`;
  let text = `<h1>${timestamp}</h1>`;
  text += list.innerHTML.replace(/"="" /g, ``);
  let blob = new Blob([text], {type: `text/html;charset=utf-8`});
  saveAs(blob, `${name}-${timestamp}.html`);
}

const getReports = function() {
  fetch(`app/waybatch.php?reports`, {
    method: `GET`,
    credentials: `same-origin`
  })
  .then(response => response.json())
  .then(data => {
    modalTitle.innerHTML = `<h5>Generated reports: ${Object.keys(data).length}</h5>`;
    for(let file in data) {
      modalContent.innerHTML = `<a href="${data[file]}" target="_blank">${file}</a><br>${modalContent.innerHTML}`;
    }
    modal.style.display = `block`;
  })
  .catch(error => {
    console.log(error);
  });
}

const uploadDomains = function() {
  fetch('app/waybatch.php', {
    method: 'POST',
    credentials: `same-origin`,
    headers: {
      "Content-Type":"application/x-www-form-urlencoded"
    },
    body: `token=${token.value}&cron=${input.value}`
  })
  .then(response => response.text())
  .then(data => token.value = data)
}
