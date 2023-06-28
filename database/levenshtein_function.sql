-- search query proposal
-- select * from contractor WHERE levenshtein(UPPER(contractor), UPPER("word word")) < 4 or contractor LIKE "%ord%" or contractor LIKE "%wor%"

--  select *, levenshtein_ratio(UPPER(contractor), UPPER("MIDWEST FOUNDATIONS")) as ratio
--  from contractor
--  WHERE levenshtein(UPPER(contractor), UPPER("MIDWEST FOUNDATIONS"))
--  and levenshtein_ratio(UPPER(contractor), UPPER("MIDWEST FOUNDATIONS")) > 45
--  order by ratio desc

-- select *, levenshtein_ratio(UPPER(contractor), UPPER("MIDWEST FOUNDATIONS")) as ratio  from contractor WHERE (levenshtein(UPPER(contractor), UPPER("MIDWEST FOUNDATIONS")) and levenshtein_ratio(UPPER(contractor), UPPER("MIDWEST FOUNDATIONS")) > 45) or contractor LIKE "%IDWES%" or contractor LIKE "%OUNDATION%" order by ratio desc

CREATE FUNCTION levenshtein_ratio( s1 VARCHAR(255), s2 VARCHAR(255) )
  RETURNS INT
  DETERMINISTIC
  BEGIN
    DECLARE s1_len, s2_len, max_len INT;
    SET s1_len = LENGTH(s1), s2_len = LENGTH(s2);
    IF s1_len > s2_len THEN
      SET max_len = s1_len;
    ELSE
      SET max_len = s2_len;
    END IF;
    RETURN ROUND((1 - LEVENSHTEIN(s1, s2) / max_len) * 100);
  END;


CREATE FUNCTION levenshtein( s1 VARCHAR(255), s2 VARCHAR(255) ) 
  RETURNS INT 
  DETERMINISTIC 
  BEGIN 
    DECLARE s1_len, s2_len, i, j, c, c_temp, cost INT; 
    DECLARE s1_char CHAR; 
    -- max strlen=255 
    DECLARE cv0, cv1 VARBINARY(256); 
    SET s1_len = CHAR_LENGTH(s1), s2_len = CHAR_LENGTH(s2), cv1 = 0x00, j = 1, i = 1, c = 0; 
    IF s1 = s2 THEN 
      RETURN 0; 
    ELSEIF s1_len = 0 THEN 
      RETURN s2_len; 
    ELSEIF s2_len = 0 THEN 
      RETURN s1_len; 
    ELSE 
      WHILE j <= s2_len DO 
        SET cv1 = CONCAT(cv1, UNHEX(HEX(j))), j = j + 1; 
      END WHILE; 
      WHILE i <= s1_len DO 
        SET s1_char = SUBSTRING(s1, i, 1), c = i, cv0 = UNHEX(HEX(i)), j = 1; 
        WHILE j <= s2_len DO 
          SET c = c + 1; 
          IF s1_char = SUBSTRING(s2, j, 1) THEN  
            SET cost = 0; ELSE SET cost = 1; 
          END IF; 
          SET c_temp = CONV(HEX(SUBSTRING(cv1, j, 1)), 16, 10) + cost; 
          IF c > c_temp THEN SET c = c_temp; END IF; 
            SET c_temp = CONV(HEX(SUBSTRING(cv1, j+1, 1)), 16, 10) + 1; 
            IF c > c_temp THEN  
              SET c = c_temp;  
            END IF; 
            SET cv0 = CONCAT(cv0, UNHEX(HEX(c))), j = j + 1; 
        END WHILE; 
        SET cv1 = cv0, i = i + 1; 
      END WHILE; 
    END IF; 
    RETURN c; 
  END; 