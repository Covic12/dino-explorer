const API_BASE_URL = "http://localhost/dino-explorer/backend/api";

class DinoAPI {
  constructor() {
    this.token = localStorage.getItem("auth_token");
    this.user = JSON.parse(localStorage.getItem("user") || "null");
  }

  getHeaders(includeAuth = false) {
    const headers = {
      "Content-Type": "application/json",
    };

    if (includeAuth && this.token) {
      headers["Authorization"] = `Bearer ${this.token}`;
    }

    return headers;
  }

  async request(endpoint, options = {}) {
    try {
      const response = await fetch(`${API_BASE_URL}${endpoint}`, {
        ...options,
        headers: this.getHeaders(options.auth),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.error || "Request failed");
      }

      return data;
    } catch (error) {
      console.error("API Error:", error);
      throw error;
    }
  }

  async register(username, email, password, role = "user") {
    const data = await this.request("/auth/register", {
      method: "POST",
      body: JSON.stringify({ username, email, password, role }),
    });
    return data;
  }

  async login(username, password) {
    const data = await this.request("/auth/login", {
      method: "POST",
      body: JSON.stringify({ username, password }),
    });

    if (data.token) {
      this.token = data.token;
      this.user = data.user;
      localStorage.setItem("auth_token", data.token);
      localStorage.setItem("user", JSON.stringify(data.user));
    }

    return data;
  }

  logout() {
    this.token = null;
    this.user = null;
    localStorage.removeItem("auth_token");
    localStorage.removeItem("user");
  }

  isAuthenticated() {
    return !!this.token;
  }

  isAdmin() {
    return this.user && this.user.role === "admin";
  }

  async getDinosaurs() {
    return this.request("/dinosaurs");
  }

  async getDinosaur(id) {
    return this.request(`/dinosaurs/${id}`);
  }

  async createDinosaur(dinosaur) {
    return this.request("/dinosaurs", {
      method: "POST",
      body: JSON.stringify(dinosaur),
      auth: true,
    });
  }

  async updateDinosaur(id, dinosaur) {
    return this.request(`/dinosaurs/${id}`, {
      method: "PUT",
      body: JSON.stringify(dinosaur),
      auth: true,
    });
  }

  async deleteDinosaur(id) {
    return this.request(`/dinosaurs/${id}`, {
      method: "DELETE",
      auth: true,
    });
  }

  async getEras() {
    return this.request("/eras");
  }

  async getEra(id) {
    return this.request(`/eras/${id}`);
  }

  async createEra(era) {
    return this.request("/eras", {
      method: "POST",
      body: JSON.stringify(era),
      auth: true,
    });
  }

  async updateEra(id, era) {
    return this.request(`/eras/${id}`, {
      method: "PUT",
      body: JSON.stringify(era),
      auth: true,
    });
  }

  async deleteEra(id) {
    return this.request(`/eras/${id}`, {
      method: "DELETE",
      auth: true,
    });
  }

  async getLocations() {
    return this.request("/locations");
  }

  async getLocation(id) {
    return this.request(`/locations/${id}`);
  }

  async createLocation(location) {
    return this.request("/locations", {
      method: "POST",
      body: JSON.stringify(location),
      auth: true,
    });
  }

  async updateLocation(id, location) {
    return this.request(`/locations/${id}`, {
      method: "PUT",
      body: JSON.stringify(location),
      auth: true,
    });
  }

  async deleteLocation(id) {
    return this.request(`/locations/${id}`, {
      method: "DELETE",
      auth: true,
    });
  }

  async getResearchers() {
    return this.request("/researchers");
  }

  async getResearcher(id) {
    return this.request(`/researchers/${id}`);
  }

  async createResearcher(researcher) {
    return this.request("/researchers", {
      method: "POST",
      body: JSON.stringify(researcher),
      auth: true,
    });
  }

  async updateResearcher(id, researcher) {
    return this.request(`/researchers/${id}`, {
      method: "PUT",
      body: JSON.stringify(researcher),
      auth: true,
    });
  }

  async deleteResearcher(id) {
    return this.request(`/researchers/${id}`, {
      method: "DELETE",
      auth: true,
    });
  }
}

const api = new DinoAPI();
