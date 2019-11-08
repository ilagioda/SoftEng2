package model;

public class Ticket {

	private String id;
	private String timestamp;
	private Counter c;
	
	public Ticket(String id, String timestamp) {
		super();
		this.id = id;
		this.timestamp = timestamp;
		this.c = null;
	}
	
	public Counter getC() {
		return c;
	}

	public void setC(Counter c) {
		this.c = c;
	}

	public String getId() {
		return id;
	}
	public void setId(String id) {
		this.id = id;
	}
	public String getTimestamp() {
		return timestamp;
	}
	public void setTimestamp(String timestamp) {
		this.timestamp = timestamp;
	}

	@Override
	public String toString() {
		/*
		 * Returns something like:
		 * Ticket a19
		 * 21-10-2019 10:22:19
		 */
		return "Ticket "+ id + " - " + timestamp;
	}

	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime * result + ((id == null) ? 0 : id.hashCode());
		result = prime * result + ((timestamp == null) ? 0 : timestamp.hashCode());
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		Ticket other = (Ticket) obj;
		if (id == null) {
			if (other.id != null)
				return false;
		} else if (!id.equals(other.id))
			return false;
		if (timestamp == null) {
			if (other.timestamp != null)
				return false;
		} else if (!timestamp.equals(other.timestamp))
			return false;
		return true;
	}
	
	
}
