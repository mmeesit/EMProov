1.	Ressurssid, mida eelkõige kasutan selliste asjade jaoks on developers.google.com ja stackoverflow.com. Google on üldiselt kõige sobilikum resurss selliste asjade jaoks, sest nemad on praegu eestvedajad kõik load-specific ja security-ga seotud asjadega, sest nemad tegelevad kõige rohkem selliste asjadega ja kirjutavad usinalt nendest.
Esmalt vaataksin serverist kui palju on päringuid tehtud, kas on tegu naturaalse ülekoormusega või keegi üritab pahatahtlikult üle koormata servereid, ehk DDOS-da. Probleem võib ka tulla sellest, et uus bänner on nii mahukas, et tavalised malicious requestid ennist ei ületanud serveri maximus load thresholdi, aga nüüd see load sai ületatud. Võtaksin ette serveri logid ja vaataksin kui suur koormus on ja palju päringuid on. Kui maliciousness-aspekt on välistatud siis võtaksin ette standardid, et vaadata kas kliendi bänner oma ressursside mahult ei ületaks maksimum soovitatud nõudeid (a la Google soovitatud technical specifications third-party served banneritele) initial load size: 150KB ja total initiated load size 2.2MB. Hea tool, mida kasutada selle jaoks, on Lighthouse Network Payloads audit, mis näitab ära network requestide suurused. Kui need nõuded ei ole täidetud, siis soovitan kliendil pildid, fondid ja CSS failide suurusi vähendada sobilike compression tööriistadega. Näiteks: https://www.minifier.org/ abil. Piltide jaoks https://tinyjpg.com/
Veel on variant kasutada online image optimizer tööriistu, mis on lihts
2.	

