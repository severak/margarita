BEGIN TRANSACTION;
CREATE TABLE agency (agency_id INTEGER PRIMARY KEY, agency_name TEXT, agency_timezone TEXT, agency_url TEXT);
CREATE TABLE routes (agency_id NUMERIC, route_id INTEGER PRIMARY KEY, route_long_name TEXT, route_short_name TEXT, route_type NUMERIC);
CREATE TABLE stop_times (stop_times_id INTEGER PRIMARY KEY, arrival_time NUMERIC, departue_time NUMERIC, stop_id NUMERIC, stop_sequence NUMERIC, trip_id NUMERIC);
CREATE TABLE stops (stop_code TEXT, stop_id INTEGER PRIMARY KEY, stop_lat NUMERIC, stop_lon NUMERIC, stop_name TEXT, transfer INTEGER);
CREATE TABLE trips (route_id NUMERIC, service_id NUMERIC, trip_id INTEGER PRIMARY KEY, trip_short_name TEXT, trip_headsign TEXT);
COMMIT;
