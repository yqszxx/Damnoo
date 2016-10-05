var isshowinput = false;
var color;
var click=false;

// yq WebSocket qy
var ws = new WebSocket("ws://192.168.0.190:1919");

ws.onopen = function(){
    console.log("握手成功");
    //ws.send('LoGiN2000919');
};
ws.onmessage = function(e){
    var jj = e.data;
    //console.log(text);
    damoo.emit(JSON.parse(jj));
};
// yq end qy

//背景图随机
var b=window.document.getElementsByTagName("body");
var count=6;
var now=0;
window.onload=function (){
    now=Math.round(Math.random()*count);
    b.item(0).style.backgroundImage="url('img/bg"+now+".jpg')";
}

function change(){
    var n=Math.round(Math.random()*(count-1));
    if (n>=now) now =++n;
    b.item(0).style.backgroundImage="url('img/bg"+now+".jpg')";
}
//
function pushError(text){
    $("#error").fadeIn(1000);
    $("#error").html(text);
    setTimeout(function (){
        $("#error").fadeOut(1000);
    },2000);
}
//弹幕部分
var scrn = window.document.getElementById('dm-screen');
(function(window) {
    scrn.style.width = window.innerWidth + "px";
    scrn.style.height = window.innerHeight + "px";
    scrn.style.opacity = 1;
})(window);

var damoo = Damoo('dm-screen', 'dm-canvas', 24, "黑体");

$(window).resize(function(){
    scrn.style.width = window.innerWidth + "px";
    scrn.style.height = window.innerHeight + "px";
    scrn.style.opacity = 1;
    damoo.resize();
});

damoo.start();

var addEvent = function(obj, nm, cb) {
    if (window.addEventListener) {
        obj.addEventListener(nm, cb, false);
    } else if (window.attachEvent) {
        obj.attachEvent("on" + nm, cb);
    }
};

damoo.emit({ text: "SYS:欢迎~", color: "#" + Math.random().toString(16).substring(2).substring(0, 6) });

addEvent(document.body, "keypress", function(e) {
    var keyCode = e.keyCode || e.which;
    switch (keyCode) {
        case 13:
            if (!isshowinput){
                $("#sender").fadeIn(1000);
                isshowinput=true;
            }else{
                var name=document.getElementById("name").value;
                if(name==""){
                    pushError("SYS:请先填写您的名字");
                }else if(name.length>15){
                    pushError("SYS:名字太长啦");
                }else{
                    var text=document.getElementById("damoo").value;
                    document.getElementById("damoo").value="";
                    alert(color);
                    ws.send(JSON.stringify({ text: name+":"+text, color: color, fixed: click}));
                    //damoo.emit({ text: name+":"+text, color: color, fixed: click});
                    $("#sender").fadeOut(1000);
                    isshowinput=false;
                }
            }
            break;
        case 47:
            damoo.clear();
            break;
        case 32:
            if (damoo.state) {
                damoo.suspend();
            } else {
                damoo.resume();
            }
            break;
    }
    return false;
});

//取色器

$('.color-box').colpick({
	colorScheme:'light',
	layout:'hex',
	color:'ffffff',
	onSubmit:function(hsb,hex,rgb,el) {
        color='#'+hex;
		$(el).css('background-color', '#'+hex);
		$(el).colpickHide();
	}
}).css('background-color', '#ffffff');

//
$("#checkbox").click(function(){
    if(!click){
        $('#checkbox').removeClass("be");
        $('#checkbox').addClass("new");
        click=true;
    }else{
        $('#checkbox').removeClass("new");
        $('#checkbox').addClass("be");
        click=false;
    }
})