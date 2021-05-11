
document.getElementById('btn').addEventListener('click', function () {
    let data = collectData('input');
    send(data);
});

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

function validate(target)
{
    for(i = 0; arrayLength = target.length, i < arrayLength; i++) {
        if (target[i].name === 'name') {
            let re = /^[\w ]+$/;
            console.log(re.test(target[i].value));
        } else {
            let re = /^[\d]+$/;
            console.log(re.test(target[i].value));
        }
    }
}

function showResult(response)
{
    if(document.getElementById('resultTable')){document.getElementById('resultTable').remove()}
    let result = document.createElement('table');
    result.id = 'resultTable';
    result.style.textAlign = 'left';

    for (const [key, value] of Object.entries(response)) {
        result.innerHTML += '<tr><th>' + key + ':' + '</th></tr>';

        if(value.length){
            for (let [keyy, valuee] of Object.entries(value)) {
                result.innerHTML += '<tr><th>' + 'Object name - ' + valuee['name'] + '</th></tr>';

                buildRowByObject(valuee, result);
            }
        } else {
            buildRowByObject(value, result);
        }
        result.innerHTML += "<hr>";
    }

    document.getElementById('mainDiv').appendChild(result)
}

function buildRowByObject(value, element)
{
    for (let [key, valuee] of Object.entries(value)) {
        if(key === 'id' || key === 'name') continue;
        element.innerHTML += '<tr><td>' + key + '</td><td>' + valuee + '</td></tr>';
    }
}

function send(data)
{
    var request = new XMLHttpRequest();
    request.open('POST', '/api/find', true);
    request.setRequestHeader('Content-Type', 'application/json');
    request.responseType = 'json';
    request.onreadystatechange = function (aEvt) {
        if (request.readyState === 4) {
            if(request.status === 200) {
                showResult(request.response);
            }
            else {
                console.log(request.status);
            }
        }
    };
    request.send(data);
}