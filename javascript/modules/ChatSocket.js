// Info: Chat Socket Js

export default class ChatSocket {
  constructor({ url, user_id }) {
    this.url = url;
    this.user_id = user_id;
    this.ws = null;
    this.events = {};
    this.connect();
  }

  connect() {
    this.ws = new WebSocket(this.url);

    this.ws.onopen = () => {
      this.send({ type: "init", user_id: this.user_id });
      this.trigger("open");
    };

    this.ws.onmessage = (e) => {
      const data = JSON.parse(e.data);
      this.trigger(data.type, data);
    };

    this.ws.onclose = () => {
      this.trigger("close");
      setTimeout(() => this.connect(), 3000);
    };
  }

  send(data) {
    if (this.ws.readyState === WebSocket.OPEN) {
      this.ws.send(JSON.stringify(data));
    }
  }

  on(event, callback) {
    this.events[event] = callback;
  }

  trigger(event, data) {
    if (this.events[event]) {
      this.events[event](data);
    }
  }
}