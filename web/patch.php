if ($("#torrents tr td").length == 0) continue;

$("#torrents tr td")[job * 8 + 6].innerHTML = jobs[job]["speed"]/1024.0 + "K/s";
$("#torrents tr td")[job * 8 + 7].innerHTML = jobs[job]["eta"];
