import os, time, locations
from processpipe import ExternalProcess, ProcessException
import cherrypy

OMX_CMD_ORIG = "omxplayer --timeout 120 --aspect-ratio stretch -I --no-keys "
#OMX_CMD = "omxplayer --timeout 6000 -o both -I "
OMX_CMD = "omxplayer --hw --timeout 6000 -o both --aspect-ratio stretch -I --no-keys "
#OMX_CMD = "/usr/bin/vlc -f --quiet-synchro --no-vidoe-title-show "
#OMX_CMD = "omxplayer --hw --timeout 60s -o local --no-keys pipe:0 "

_DBUS_PATH = os.path.join(locations.BIN_PATH, "dbus.sh")
_INPUT_TIMEOUT = 10
_START_TIMEOUT = 120

class OmxplayerProcess3(ExternalProcess):

  def __init__(self):
    ExternalProcess.__init__(self, True)

  def _get_cmd(self, args):
     return self.cmd

  def name(self):
    return 'omxplayer(OmxplayerProcess3)'

  def _wait_input(self, fname):
    for i in xrange(_INPUT_TIMEOUT):
      if os.path.isfile(fname):
        return True
      time.sleep(5)
    return False

  def start(self, args):
    self.cmd = OMX_CMD
    if 'subtitles' in args:
      self.cmd = self.cmd + "--align center --subtitles '" + args['subtitles'] + "' "
    fname = args['outfile']
    #if fname.startswith('http'):
    #  self.cmd = self.cmd + "'" + fname + "'"
    if not self._wait_input(fname):
       self._set_error("Omxplayer timed out waiting for input file")
       self.msg_halted()
       return
    else:
      pid = args['pid']
      tail = "tail -f --pid=" + str(pid) + " --bytes=+0 \"" + fname + "\""
      #self.cmd = tail + ' | ' + self.cmd + 'pipe:0'
      self.cmd = tail + ' | ' + 'omxplayer --hw --timeout 60s -o both --no-keys pipe:0 '
      # Wait a bit for input
      cherrypy.log("OMXPROCESS: " + self.cmd)
      #print "self.cmd => " + self.cmd
      #f = open("/tmp/demofile3.txt", "w")
      #f.write(self.cmd)
      #f.close()
      time.sleep(10)

    ExternalProcess.start(self, args)

  def _ready(self):
    #time.sleep(20)
    #os.system(_DBUS_PATH + " status &")
    #line = os.popen(_DBUS_PATH + " status &").read()
    #cherrypy.log("OMXPROCESS: " + line)
    while True:
      #line = self._readline(_START_TIMEOUT)
      time.sleep(5)
      line = os.popen(_DBUS_PATH + " status &").read()
      #cherrypy.log("OMXPROCESS: " + line)
      if line.startswith('have a nice day'):
        raise ProcessException("omxplayer3 failed to start")
      elif "Error" in line:
        raise ProcessException("omxplayer3 failed to start")
        break
      elif line.startswith('Vcodec id unknown:'):
        raise ProcessException("Unsupported video codec")
      elif "Metadata:" in line:
        break
      elif "Duration:" in line:
        break

  def control(self, action):
    dbcmd = None
    if action == 'pause' or action == 'resume':
      dbcmd = 'pause'
    if dbcmd is not None:
      os.system(_DBUS_PATH + " " + dbcmd + " &")
