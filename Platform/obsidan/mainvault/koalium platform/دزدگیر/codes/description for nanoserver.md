code for esp8266, that as server, ssid: smartis , pass: smart12345 ,when connect user to it :show the page that has :form to :get username , password from connected device ,add the inserted user , pass , mac device , time , to table tryconnect in db , if its username password correct when check to table users that has user , pass , lvl , situate,state,address,config,description,lastlog,, read lvl from db and if lvl less than 5, show control dashboard to user , else show just monitoring dashboard to. that control dashboard contain monitoring at the top pager, in monitoring: show 8 lamp state as on or of , 2 door state and windows as open or closer , system as idle or low or high sens , temprature v, humidity , pressure o0f ambient that all readed from database table sensoresdata, control panel after monitoring has 8 toggle btn for on-off the 8 seprat lams and air condition on-off and auto and set temp for ac from 10 to 30 and a large red btn to set ajir and wtf state to alarm mode.and check db to it authen , if correct then according to lvl that readed from db, if greater than 3 then show control panel in its dashboard by 8 toggle btn to on-off 8 lamp and the aircondition controller and show the ambient temp and humidity that readed from db and all of the interfer of user add to db





add uart serial comunication to it to send data and recive from sim800 modules , that when recived sms send it to servere and if correct phone num then check its instruction by db instructions means


add sd card handler to save db in it that has table for insert each of pins state in it every 60 seconds

another file that save each change and comiunication and log all things

ambient pressure and temp and humidity read from bmp280 module and saved to db table each 1 minute , 3 sitch add to 3 pins as input and if each switch opened it means a window open and another 1 sitch for door and if state of them changed save its new state and all of pin states on db by its datetime

