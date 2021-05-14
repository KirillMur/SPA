
document.getElementById('btn').addEventListener('click', function () {
    removeElement('resultTable');
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

function showResult(response)
{
    removeElement('resultTable');
    let result = document.createElement('table');
    result.id = 'resultTable';
    result.style.textAlign = 'left';

    for (const [key, value] of Object.entries(response)) {
        result.innerHTML += '<tr><th class="orange">' + key + ':' + '</th></tr>';

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

function buildRowByObject(value, element)
{
    for (let [key, valuee] of Object.entries(value)) {
        if(key === 'name') continue;
        element.innerHTML += '<tr><td>' + key + ':</td><td>' + valuee + '</td></tr>';
    }
}

function spinner(remove = false)
{
    console.log(remove);
    let spinner = document.getElementsByClassName('spinner')[0];

    if(remove){
        console.log('spinner && remove');
        setTimeout(function(){spinner.style.display = 'none'}, 500);
    } else {
        console.log('visible');
        spinner.style.display = 'inline-block';
    }
}

function removeElement(element)
{
    if(document.getElementById(element))
        document.getElementById(element).remove()
}

function send(data)
{
    spinner();
    var request = new XMLHttpRequest();
    request.open('POST', '/api/find', true);
    request.setRequestHeader('Content-Type', 'application/json');
    request.responseType = 'json';
    request.onreadystatechange = function (aEvt) {
        console.log(request.readyState);
        if (request.readyState === 4) {
            if(request.status === 200) {
                setTimeout(function(){
                    showResult(request.response);
                    spinner(true);}, 500);
            }
            else {
                console.log(request.status);
            }
        }
    };
    request.send(data);
}