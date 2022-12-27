

let allblocs = document.getElementsByClassName("rules");
for(i = 0; i < allblocs.length; i ++){
   allblocs[i].onmouseover = function(){
      this.style.background = "Silver";
   }
}
for(i = 0; i < allblocs.length; i ++){
    allblocs[i].onmouseleave = function(){
       this.style.background = "";
    }
 }

