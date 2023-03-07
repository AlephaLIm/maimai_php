import csv

with open("remasterSongData.csv", newline='', encoding="utf-8") as in_file:
    with open("remasterSongDatanew.csv", 'w', newline='',encoding="utf-8") as out_file:
        writer = csv.writer(out_file)
        for row in csv.reader(in_file):
            if row:
                writer.writerow(row)