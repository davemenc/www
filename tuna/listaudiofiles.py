import os,shutil,random,urllib
from collections import deque
import subprocess
class Queue:
    def __init__(self,items):
        self.TheQueue = deque([items])

    def add(self,item):
         self.TheQueue.append(item)

    def next(self):
        return self.TheQueue.popleft()

    def text(self):
        s=""
        for item in self.TheQueue:
            s += "{},".format(item)

    def quelen(self):
        return len(self.TheQueue)

def parse_path(root,full_path):
    subdirs = full_path[len(root)+1:]
    dirlist = subdirs.split("\\")
    return dirlist

AudioExtensions = [".mp3",".aaf",".aap",".aax",".aiff",".m4a",".m4p",".mp4",".wav",".wma"]
HideExtensions = [".ini",".xlsx",".gz",".txt",".jpg",".dat",".rmj",".pls",".pdf"]

firstdir = os.path.join("D:\\","DEV","WWW","mymusic")
rooturl = "http://localhost/mymusic/"
directories = Queue(firstdir)
groups = {}
files = []
albums = []
extensions = []
MAXITERATIONS = 500000000
circuitbreaker = 0

fout = open("audiolist.txt","w")
fout.write("Type\tGroup\tAlbum\tTrack\tTrackNo\tURL\n")
while directories.quelen()>0:
    circuitbreaker+=1
    if circuitbreaker>MAXITERATIONS:
        break
    cur_dir = directories.next()
    group = cur_dir[len(firstdir)+1:].replace("\\","_").lower()
    
    for filename in os.listdir(cur_dir):
        full_path = os.path.join(cur_dir,filename)
        file_name, file_extension = os.path.splitext(filename)
        if (os.path.isdir(full_path)):
            directories.add(full_path)
        elif file_extension.lower() in AudioExtensions:
            dirlist = parse_path(firstdir, full_path)
            if len(dirlist)<4:
                pass # not in the right format; ignore it
            else:
                audio_type = dirlist[0]
                group = dirlist[1]
                album = dirlist[2]
                track = dirlist[3]
                try:
                    trackno = int(track[0:3])
                except:
                    trackno=0
                else:
                    track = track[3:]
                url = rooturl+urllib.quote(audio_type)+"/"+urllib.quote(group)+"/"+urllib.quote(album)+"/"+urllib.quote(filename)
                fout.write(audio_type+"\t"+group+"\t"+album+"\t"+track+"\t"+str(trackno)+"\t"+url+"\n")
    
        elif file_extension.lower() not in HideExtensions:
            extensions.append(full_path)
   

if len(extensions)>0:
    print "FILE EXTENSIONS: "
for ext in extensions:
    print ext
print "\n"


