require "parsers.csv"
require "luasql.sqlite3"


function str_replace(s, srch, rplc) -- Same as php's function
--------------------------------------------------------------------------------
	        local pos = 1
	        if (srch == nil) or (srch == '') or (s == nil) or (s == '') then
	                return s
	        end
	        -- for each srch found
	        for st, sp in function() return string.find(s, srch, pos, true) end do
	                s = string.sub(s, 1, st-1) .. rplc .. string.sub(s, sp+1)
	                pos = st + string.len(rplc) -- Jump past current rplc
	        end	
	        return s
end


function modifyField(k,v)
  if k=="trip_id" then
    return str_replace(v,"/","-")
  end
  return v
end

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

function importCsvToSQL(fn,tn,fdef)
   n=1
   for line in io.lines(fn) do
      t=fromCSV(line)
      if n==1 then
         fields=t
         fset=table.concat(fields,",")
         defs={}
         if not(fdef) then
            fdef={}
         end
         for k,v in pairs(fields) do
            print(v)
            if fdef[v] then
              defs[k]=v.." "..fdef[v]
            else
              defs[k]=v
            end
         end
         q="CREATE TABLE "..tn.." ("..table.concat(defs,",")..")"
         local res=con:execute(q)
         print(q,res)
      else
         nt={}
         for k,v in pairs(t) do
            v=modifyField(fields[k],v)
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
importCsvToSQL("szeged/agency.txt","agency",{["agency_id"]="INTEGER PRIMARY KEY",})
importCsvToSQL("szeged/routes.txt","routes",{["agency_id"]="INTEGER",["route_id"]="INTEGER PRIMARY KEY"})
importCsvToSQL("szeged/trips.txt","trips",{["trip_id"]="PRIMARY KEY",["route_id"]="INTEGER"})
importCsvToSQL("szeged/stops.txt","stops",{["stop_id"]="INTEGER PRIMARY KEY"})
importCsvToSQL("szeged/stop_times.txt","stop_times",{["stop_id"]="INTEGER",["stop_sequence"]="INTEGER",["departure_time"]="INTEGER",["arrival_time"]="INTEGER"})

con:execute("ALTER TABLE trips ADD COLUMN trip_short_name")
con:execute("UPDATE trips SET trip_short_name=trip_id")
con:execute("ALTER TABLE stops ADD COLUMN transfer")

con:close()
env:close()