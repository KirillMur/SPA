
document.getElementById('btn').addEventListener('click', function () {
    fadeElement('resultTable');
    let data = collectData('input');
    send(data);
});

//собираем данные с полей в массив
function collectData(tagname)
{
    var array = {};
    var target = document.getElementsByTagName(tagname);

    for(i = 0; arrayLength = target.length, i < arrayLength; i++){
        if(target[i].value.trim()) {
            array[target[i].name] = ''; //убираем "undefined"
            array[target[i].name] += target[i].value;
        }
    }

    return JSON.stringify(array);
}

//вывод ответа
function showResult(response)
{
    removeElement('resultTable');
    let result = document.createElement('table');
    result.id = 'resultTable';
    result.style.textAlign = 'left';

    for (const [key, value] of Object.entries(response)) {
        result.innerHTML += '<tr><th class="orange">' + key + ':</th></tr>';

        if(value.length){
            for (let [keyy, valuee] of Object.entries(value)) {
                result.innerHTML += '<tr><th>' + 'Object: ' + valuee['name'] + '</th></tr>';

                buildRowByObject(valuee, result);
            }
        } else {
            buildRowByObject(value, result);
        }
        result.innerHTML += "<hr>";
    }

    document.getElementById('mainDiv').appendChild(result)
}

//создает строки внутри таблицы
function buildRowByObject(value, element)
{
    for (let [key, valuee] of Object.entries(value)) {
        if(key === 'name') continue;
        element.innerHTML += '<tr><td>' + key + ':</td><td>' + valuee + '</td></tr>';
    }
}

//индикатор выполнения
function spinner(remove = false)
{
    let spinner = document.getElementById('spinner');

    if(remove){
        spinner.style.display = 'none';
    } else {
        spinner.style.display = 'inline-block';
    }
}

function removeElement(elementId)
{
    if(document.getElementById(elementId))
        document.getElementById(elementId).remove()
}

function fadeElement(elementId)
{
    if(document.getElementById(elementId)) {
        document.getElementById(elementId).style.color = 'gray';
        let target = document.getElementsByTagName('th');

        for(i = 0; arrayLength = target.length, i < arrayLength; i++){
            target[i].style.color = 'gray';
        }
    }
}

//для вывода сообщения в случае ошибки соединения
function errorMsg(msg)
{
    if (document.getElementById('errorMsg'))
        document.getElementById('errorMsg').remove();

    let span = document.createElement('span');
    span.id = 'errorMsg';
    span.style.marginLeft = '10px';
    span.style.color = 'orangered';
    span.style.fontWeight = 'bold';
    span.innerText = msg;
    document.getElementById('spinner').after(span);
}

function elementDisable(elementId, disable = true)
{
    document.getElementById(elementId).disabled = disable;
}

function send(data)
{
    spinner();
    elementDisable('btn');
    var request = new XMLHttpRequest();
    request.open('POST', '/api/find', true);
    request.setRequestHeader('Content-Type', 'application/json');
    request.responseType = 'json';
    request.onreadystatechange = function (aEvt) {
        if (request.readyState === 4) {
            if(request.status === 200 || request.status === 400) {
                console.log(request.response);

                setTimeout(function(){
                    showResult(request.response);
                    elementDisable('btn', false);
                    spinner(true);}, 500); //эмуляция времени поиска
            }
            else {
                errorMsg('Connection error (status code: ' + request.status + ')');
            }
        }
    };
    request.send(data);
}