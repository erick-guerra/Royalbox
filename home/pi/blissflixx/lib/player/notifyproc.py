from processpipe import ExternalProcess, ProcessException, OUT_FILE
import cherrypy

class NotificationProcess(ExternalProcess):
  _START_TIMEOUT = 5

  def __init__(self, message):
    ExternalProcess.__init__(self)
    cmd = [
      'notify-send',
      '--expire-time',
      '10000',
      message
    ]
    self.cmd = cmd 

  def name(self):
    return 'notify-send'

  def _get_cmd(self, args):
    self.args = args
    return self.cmd

  def _ready(self):
    while True:
        return {'pid':self.proc.pid}
