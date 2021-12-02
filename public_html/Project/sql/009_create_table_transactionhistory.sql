CREATE TABLE IF NOT EXISTS Transaction_History(
    id int AUTO_INCREMENT PRIMARY KEY ,
    src int,
    dest int,
    balanceChange int,
    transactionType varchar(15) not null COMMENT 'The type of transaction that occurred',
    memo varchar(240) default null COMMENT  'user-defined notes',
    expectedTotal int,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (src) REFERENCES Accounts(id),
    FOREIGN KEY(dest) REFERENCES Accounts(id),
    constraint ZeroTransferNotAllowed CHECK(balanceChange != 0)
)