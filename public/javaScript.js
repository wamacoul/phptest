var row = document.getElementById('row');
let children = row.children;
var position = 0;
hideBook()
animation();
function hideBook()
{
    for(var i=0; i<children.length; i++)
    {
        children[i].style.display = "none";
    }
}
function animation()
{
    children[position].style.display = "block";
    position++;
    setTimeout(animation,1000);
}
