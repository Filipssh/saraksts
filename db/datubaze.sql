# lietotājs
CREATE TABLE lietotajs(
    lietotajvards varchar(30) NOT NULL PRIMARY KEY,
    parole varchar(255) NOT NULL,
    epasts varchar(50) NOT NULL,
    tel_nr varchar(15),
    loma varchar(15),
    registrejies datetime DEFAULT current_timestamp()
);
# saraksts
CREATE TABLE saraksts(
    id int(11) NOT NULL AUTO_INCREMENT,
    nosaukums varchar(100) NOT NULL,
    lietotajvards varchar(30),
    PRIMARY KEY (id),
    FOREIGN KEY (lietotajvards) REFERENCES lietotajs(lietotajvards)
);
# ieraksts
CREATE TABLE ieraksts(
    id int(11) NOT NULL AUTO_INCREMENT,
    teksts varchar(100) NOT NULL,
    izsvitrots BOOLEAN DEFAULT 0,
    saraksts_id int(11),
    PRIMARY KEY (id),
    FOREIGN KEY (saraksts_id) REFERENCES saraksts(id)
);

INSERT INTO `lietotajs` (`lietotajvards`, `parole`, `epasts`, `tel_nr`, `loma`, `registrejies`) VALUES
('admin', '$argon2i$v=19$m=65536,t=4,p=1$b2VXY3JXeS9JQWZhTlVVaQ$QVk44orlMnT680242ub7icRjdi4oFyvEY1eqpxPWIE4', 'admin@admin.lv', '12345678', 'admin', '2023-10-18 20:41:23');