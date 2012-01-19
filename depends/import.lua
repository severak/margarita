require "parsers.csv"
require "luasql.sqlite3"

function esc(d)
   if string.match(d,'^%d+$') then
      return d
   elseif d=="" then
      return "NULL"
   elseif string.match(d,'^%d+:%d+:%d+$') then
      hh,mm,ss=string.match(d,'^(%d+):(%d+):(%d+)$')
      return hh*mm
   else
      return string.format('%q',d)
   end          
end

function importCsvToSQL(fn,tn)
   n=1
   for line in io.lines(fn) do
      t=fromCSV(line)
      if n==1 then
         fields=t
         fset=table.concat(fields,",")
         q="CREATE TABLE "..tn.." ("..fset..")"
         local res=con:execute(q)
         print(q,res)
      else
         nt={}
         for k,v in pairs(t) do
            nt[k]=esc(v)          
         end
         local q="INSERT INTO "..tn.."("..fset..") VALUES ("..table.concat(nt,",")..")"
         --print(q)
         local res,err=con:execute(q)
         if res then
            if res>0 then
               print("ok ",tn,n)
            end
         else
            print(err)
            print("Q: "..q)
         end
      end
      n=n+1
   end      
end

env=luasql.sqlite3()
con=env:connect("szeged/szeged.db3")
print(esc("4:15:00"))
--print(esc("1"),esc("2a"))
--importCsvToSQL("szeged/agency.txt","agency")
--importCsvToSQL("szeged/routes.txt","routes")
--importCsvToSQL("szeged/trips.txt","trips")
--importCsvToSQL("szeged/stops.txt","stops")
importCsvToSQL("szeged/stop_times_demo.txt","stop_times_demo")
con:close()
env:close()